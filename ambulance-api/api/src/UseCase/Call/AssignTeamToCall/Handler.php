<?php

declare(strict_types=1);

namespace App\UseCase\Call\AssignTeamToCall;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Collections\CustomFieldsValuesCollection;
use AmoCRM\Collections\Leads\LeadsCollection;
use AmoCRM\Collections\NotesCollection;
use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Helpers\EntityTypesInterface;
use AmoCRM\Models\CustomFieldsValues\MultitextCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\TextCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\TextCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueModels\BaseEnumCodeCustomFieldValueModel;
use AmoCRM\Models\CustomFieldsValues\ValueModels\TextCustomFieldValueModel;
use AmoCRM\Models\LeadModel;
use AmoCRM\Models\NoteType\CommonNote;
use App\Entity\Calling\Calling;
use App\Entity\Calling\Status;
use App\Flusher;
use App\Repository\CallingRepository;
use App\Repository\CityRepository;
use App\Repository\ClientRepository;
use App\Repository\PartnerRepository;
use App\Services\AmoCRM;
use App\Services\CallingSender;
use App\Services\TelegramSender;
use App\Services\TrackerToMkad;
use App\Services\WSClient;
use App\Services\YaGeolocation\Api;
use DateTimeImmutable;
use Doctrine\ORM\NonUniqueResultException;
use DomainException;
use Exception;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class Handler
{
    private AmoCRMApiClient $client;

    public function __construct(
        AmoCRM $amoCRM,
        private readonly CallingRepository $calls,
        private readonly Flusher $flusher,
        private readonly Api $geocodingApi,
        private readonly TrackerToMkad $trackerToMkad,
        private readonly PartnerRepository $partners,
        private readonly CityRepository $cities,
        private readonly \App\UseCase\Partner\Create\Handler $partnerHandler,
        private readonly ClientRepository $clients,
        private readonly \App\UseCase\Client\Create\Handler $clientHandler,
        private readonly CallingSender $sender,
        private readonly WSClient $wsClient,
        private readonly TelegramSender $tgSender,
    ) {
        $this->client = $amoCRM->getClient();
    }

    public function handle(Command $command): void
    {
        try {
            $lead = $this->client->leads()->getOne($command->externalId, [LeadModel::CONTACTS]);
        } catch (AmoCRMApiException $e) {
            throw new DomainException('Не удалось получить лид id' . $command->externalId . ' ' . $e->getMessage());
        }

        if (!$lead) {
            throw new DomainException('Не найден лид');
        }

        $call = $this->calls->findOneByNumber($command->externalId);
        [$clientName, $clientPhone] = $this->getContactData($lead);

        $call->setUpdatedAt(new DateTimeImmutable());

        try {
            $this->setClient($call, $clientName, $clientPhone);
        } catch (Exception $e) {
            $this->sendMessageToAmo((int)$command->externalId, 'Ошибка при определении клиента');
            throw new DomainException('Ошибка при определении клиента ' . $e->getMessage());
        }
        if (!$call->getClient()) {
            $this->sendMessageToAmo((int)$command->externalId, 'Клиент не определен');
            throw new DomainException('Клиент не определен');
        }

        try {
            $this->setCity($call, $lead);
        } catch (Exception $e) {
            $this->sendMessageToAmo((int)$command->externalId, 'Не удалось определить город');
            throw new DomainException('Ошибка при определении города ' . $e->getMessage());
        }

        $this->updateAddress($call, $lead);

        try {
            $this->setPartner($call, $lead);
        } catch (Exception $e) {
            $this->sendMessageToAmo((int)$command->externalId, 'Не удалось назначить партнера');
            throw new DomainException('Ошибка при определении партнера ' . $e->getMessage());
        }

        if (!$call->getPartner()) {
            $this->sendMessageToAmo((int)$command->externalId, 'Партнер не определен');
            throw new DomainException('Партнер не определен');
        }

        $this->updateData($call, $lead);

        $call->setStatus(Status::assigned());

        $this->flusher->flush();

        $message = $this->createMessage($call);

        $this->sendMessageToAmo((int)$command->externalId, $message);

        $this->sendCrmEmployees($call, $lead);

        if ($call->getAdmin()) {
            $this->tgSender->send($call->getAdmin(), "‼️️️️ ВНИМАНИЕ ‼️\nУ вас новый вызов, зайдите в приложение");
        }

        if ($call->getDoctor()) {
            $this->tgSender->send($call->getDoctor(), "‼️️️️ ВНИМАНИЕ ‼️\nУ вас новый вызов, зайдите в приложение");
        }

        if ($call->getTeam()?->getDriver()) {
            $this->tgSender->send($call->getTeam()->getDriver(), "‼️️️️ ВНИМАНИЕ ‼️\nУ вас новый вызов, зайдите в приложение");
        }

        $this->sender->sendToAdmin(
            $call,
            'Внимание новый заказ',
            $call->getAddress()
        );

        $this->wsClient->sendUpdateOffer($call->getId());
    }

    public function sendMessageToAmo(int $leadId, string $message): void
    {
        $notesCollection = new NotesCollection();
        $messageNote = new CommonNote();
        $messageNote->setEntityId($leadId)
            ->setText($message)
            ->setCreatedBy(0);

        $notesCollection->add($messageNote);

        try {
            $leadNotesService = $this->client->notes(EntityTypesInterface::LEADS);
            $leadNotesService->add($notesCollection);
        } catch (AmoCRMApiException $e) {
        }
    }

    private function getContactData(LeadModel $lead): array
    {
        $name = null;
        $phone = null;

        if (!$lead->getMainContact()) {
            return [$name, $phone];
        }

        try {
            $contact = $this->client->contacts()->getOne($lead->getMainContact()->getId());
        } catch (AmoCRMApiException $e) {
            throw new DomainException(
                'Не удалось получить контакт id' . $lead->getMainContact()->getId() . ' ' . $e->getMessage()
            );
        }

        if (!$contact) {
            throw new DomainException('Не найден контакт');
        }

        $name = $contact->getName();

        try {
            /** @var MultitextCustomFieldValuesModel $field */
            foreach ($contact->getCustomFieldsValues() as $field) {
                if ($field->getFieldId() === 604157) {
                    $phone = $field->getValues()?->first()->getValue();
                }
            }
        } catch (Exception $e) {
            throw new DomainException('Ошибка получения телефона ' . $e->getMessage());
        }

        return [$name, $phone];
    }

    private function updateData(Calling $call, LeadModel $lead): void
    {
        foreach ($lead->getCustomFieldsValues() as $field) {
            if ($field->getFieldId() === 880453) {
                try {
                    $dateTime = $field->getValues()?->first()->getValue()?->toString();
                    $call->setDateTime(new DateTimeImmutable((string)$dateTime));
                } catch (Exception) {
                }
            }
            if ($field->getFieldId() === 968865) {
                $call->setAddressInfo($field->getValues()?->first()->getValue());
            }

            if ($field->getFieldId() === 880527) {
                $call->setNosology($field->getValues()?->first()->getValue());
            }

            if ($field->getFieldId() === 870907) {
                $call->setAge($field->getValues()?->first()->getValue());
            }

            if ($field->getFieldId() === 870945) {
                $call->setDescription($field->getValues()?->first()->getValue() ?: '');
            }

            if ($field->getFieldId() === 884333) {
                $call->setChronicDiseases($field->getValues()?->first()->getValue());
            }

            if ($field->getFieldId() === 960101) {
                $call->setLeadType($field->getValues()?->first()->getValue());
            }

            if ($field->getFieldId() === 896921) {
                $call->setSendPhone($field->getValues()?->first()->getValue());
            }

            if ($field->getFieldId() === 968691) {
                $call->setPartnerHospitalization($field->getValues()?->first()->getValue());
            }

            if ($field->getFieldId() === 968867) {
                $call->setNoBusinessCards($field->getValues()?->first()->getValue());
            }

            if ($field->getFieldId() === 966615) {
                $call->setPersonal($field->getValues()?->first()->getValue());
            }

            if ($field->getFieldId() === 970557) {
                $call->setDoNotHospitalize($field->getValues()?->first()->getValue());
            }
        }
    }

    private function updateAddress(?Calling $call, LeadModel $lead): void
    {
        $address = $this->getAddress($lead);

        if (!$address) {
            return;
        }
        if ($call->getAddress() === $address) {
            return;
        }

        $call->setAddress($address);
        try {
            $geolocation = $this->geocodingApi->getPositionByAddress($address);
            if ($geolocation) {
                $call->setLat($geolocation->getLat());
                $call->setLon($geolocation->getLon());
            }

            $distance = $this->trackerToMkad->getDistance(
                (float)$geolocation->getLat(),
                (float)$geolocation->getLon(),
                $call->getCity()?->getId()
            );

            $call->setMkadDistance($distance);
        } catch (
            ClientExceptionInterface|DecodingExceptionInterface|Exception|RedirectionExceptionInterface|ServerExceptionInterface|TransportExceptionInterface
        ) {
        }
    }

    private function getAddress(LeadModel $lead): ?string
    {
        if (!$lead->getCustomFieldsValues()) {
            return null;
        }

        foreach ($lead->getCustomFieldsValues() as $field) {
            if ($field->getFieldId() === 870903) {
                return $field->getValues()?->first()->getValue();
            }
        }
        return null;
    }

    /**
     * @throws NonUniqueResultException
     */
    private function setPartner(?Calling $call, LeadModel $lead): void
    {
        [$externalId, $name] = $this->getPartnerInfo($lead);

        if (!$externalId) {
            return;
        }

        if ($call->getPartner()?->getExternalId() === $externalId) {
            return;
        }

        $partner = $this->partners->findOneByExternalId($externalId);
        if (!$partner) {
            $partnerCommand = new \App\UseCase\Partner\Create\Command(
                $name,
                $externalId
            );
            $this->partnerHandler->handle($partnerCommand);
            $partner = $this->partners->findOneByExternalId($externalId);
        }

        $call->setPartner($partner);
    }

    private function getPartnerInfo(LeadModel $lead): array
    {
        $externalId = null;
        $name = null;

        if (!$lead->getCustomFieldsValues()) {
            return [$externalId, $name];
        }

        foreach ($lead->getCustomFieldsValues() as $field) {
            if ($field->getFieldId() === 882361) {
                $first = $field->getValues()?->first();

                if ($first instanceof BaseEnumCodeCustomFieldValueModel) {
                    $externalId = $first->getEnumId() ? (string)$first->getEnumId() : null;
                }
                $name = $field->getValues()?->first()->getValue();
                return [$externalId, $name];
            }
        }
        return [$externalId, $name];
    }

    /**
     * @throws NonUniqueResultException
     */
    private function setClient(Calling $call, ?string $clientName, ?string $clientPhone): void
    {
        if (!$clientName && !$clientPhone) {
            return;
        }

        if ($call->getClient()?->getPhone() === $clientPhone) {
            return;
        }

        $client = $this->clients->findByPhone($clientPhone);

        if (!$client) {
            $clientCommand = new \App\UseCase\Client\Create\Command(
                $clientName,
                $clientPhone,
            );
            $this->clientHandler->handle($clientCommand);
            $client = $this->clients->findByPhone($clientPhone);
        }

        $call->setName($clientName);
        $call->setPhone($clientPhone);

        $call->setClient($client);
    }

    private function getTeamId(LeadModel $lead): ?string
    {
        if (!$lead->getCustomFieldsValues()) {
            return null;
        }

        foreach ($lead->getCustomFieldsValues() as $field) {
            if ($field->getFieldId() === 875863) {
                return $field->getValues()?->first()->getValue();
            }
        }
        return null;
    }

    private function createMessage(Calling $call): string
    {
        $message = '';
        $message .= 'Заявка #: ' . $call->getNumberCalling() . PHP_EOL;
        $message .= 'Тип заявки: ' . $call->getLeadType() . PHP_EOL;
        $message .= 'Врач: ' . $call->getTeam()?->getDoctor()?->getName() . PHP_EOL;
        $message .= 'Администратор: ' . $call->getTeam()?->getAdmin()?->getName() . PHP_EOL;
        $message .= 'Время прибытия: ' . $call->getDateTime()?->format('d.m.Y H:i') . PHP_EOL . PHP_EOL;
        $message .= 'Адрес: ' . $call->getAddress() . ' ' . $call->getAddressInfo() . PHP_EOL . PHP_EOL;
        $message .= 'Нозология: ' . $call->getNosology() . PHP_EOL;
        $message .= 'Возраст: ' . $call->getAge() . PHP_EOL;
        $message .= 'ХЗ: ' . $call->getChronicDiseases() . PHP_EOL . PHP_EOL;
        $message .= 'Примечание: ' . $call->getDescription() . PHP_EOL;
        return $message;
    }

    private function sendCrmEmployees(Calling $call, LeadModel $lead): void
    {
        $medTeam = $call->getTeam();
        if (!$medTeam) {
            return;
        }

        if (!$medTeam->getDoctor() && !$medTeam->getAdmin()) {
            return;
        }

        $leadCustomFieldsValues = new CustomFieldsValuesCollection();

        if ($medTeam->getDoctor()) {
            $textCustomFieldValueModel = new TextCustomFieldValuesModel();
            $textCustomFieldValueModel->setFieldId(873881);
            $textCustomFieldValueModel->setValues(
                (new TextCustomFieldValueCollection())
                    ->add((new TextCustomFieldValueModel())->setValue($medTeam->getDoctor()->getName()))
            );
            $leadCustomFieldsValues->add($textCustomFieldValueModel);
        }

        if ($medTeam->getAdmin()) {
            $textCustomFieldValueModel = new TextCustomFieldValuesModel();
            $textCustomFieldValueModel->setFieldId(873879);
            $textCustomFieldValueModel->setValues(
                (new TextCustomFieldValueCollection())
                    ->add((new TextCustomFieldValueModel())->setValue($medTeam->getAdmin()->getName()))
            );
            $leadCustomFieldsValues->add($textCustomFieldValueModel);
        }

        $lead->setCustomFieldsValues($leadCustomFieldsValues);
        $leadCollection = new LeadsCollection();

        $leadCollection->add($lead);

        try {
            $this->client->leads()->update($leadCollection);
        } catch (AmoCRMApiException) {
            throw new DomainException('Ошибка отправки данных по бригаде в CRM');
        }
    }

    private function setCity(?Calling $call, LeadModel $lead): void
    {
        $externalId = $this->getCityExternalId($lead);

        if (!$externalId) {
            return;
        }

        if ($call->getCity()?->getExternalId() === $externalId) {
            return;
        }

        $city = $this->cities->findOneByExternalId($externalId);
        if ($city) {
            $call->setCity($city);
        }
    }

    private function getCityExternalId(LeadModel $lead): ?string
    {
        if (!$lead->getCustomFieldsValues()) {
            return null;
        }

        foreach ($lead->getCustomFieldsValues() as $field) {
            if ($field->getFieldId() === 970589) {
                $first = $field->getValues()?->first();

                if ($first instanceof BaseEnumCodeCustomFieldValueModel) {
                    return $first->getEnumId() ? (string)$first->getEnumId() : null;
                }
            }
        }
        return null;
    }
}
