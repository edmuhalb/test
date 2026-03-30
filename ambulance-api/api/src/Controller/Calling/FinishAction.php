<?php

declare(strict_types=1);

namespace App\Controller\Calling;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Collections\ContactsCollection;
use AmoCRM\Collections\CustomFieldsValuesCollection;
use AmoCRM\Collections\Leads\LeadsCollection;
use AmoCRM\Collections\NotesCollection;
use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Filters\EntitiesLinksFilter;
use AmoCRM\Filters\LeadsFilter;
use AmoCRM\Helpers\EntityTypesInterface;
use AmoCRM\Models\CompanyModel;
use AmoCRM\Models\ContactModel;
use AmoCRM\Models\CustomFieldsValues\DateTimeCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\DateTimeCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueModels\DateTimeCustomFieldValueModel;
use AmoCRM\Models\LeadModel;
use AmoCRM\Models\LinkModel;
use AmoCRM\Models\NoteType\CommonNote;
use App\Entity\Calling\Calling;
use App\Entity\Calling\Row;
use App\Flusher;
use App\Repository\CallingRepository;
use App\Services\AmoCRM;
use App\Services\ATS\BlacklistService\McnBlacklistService;
use App\Services\BuhClient;
use App\Services\Call\OperatorReward;
use App\Services\Call\PartnerReward;
use App\Services\CallingSender;
use App\Services\WSClient;
use App\UseCase\Call\AddOrUpdateRepeat\Command;
use App\UseCase\Call\AddOrUpdateRepeat\Handler;
use DateTimeImmutable;
use DateTimeZone;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[AsController]
class FinishAction extends AbstractController
{
    private AmoCRMApiClient $client;

    public function __construct(
        AmoCRM $amoCRM,
        private readonly CallingSender $sender,
        private readonly PartnerReward $partnerReward,
        private readonly OperatorReward $operatorReward,
        private readonly Flusher $flusher,
        private readonly WSClient $wsClient,
        private readonly Handler $handler,
        private readonly \App\Asterisk\UseCase\Channel\DeleteByClientPhone\Handler $asteriskDeleteHandler,
        private readonly McnBlacklistService $mcnBlacklistService,
        private readonly BuhClient $buhClient,
    ) {
        $this->client = $amoCRM->getClient();
    }

    public function __invoke(Calling $calling, CallingRepository $callings, Flusher $flusher): JsonResponse
    {
        $price = 0;
        $paymentNextOrder = 0;
        $paymentHospitalization = 0;

        /** @var Row $serviceRow */
        foreach ($calling->getServices() as $serviceRow) {
            if ($serviceRow->getService()->getType() === 'default') {
                $price += $serviceRow->getPrice() !== null ? (int)$serviceRow->getPrice() : 0;
            } elseif ($serviceRow->getService()->getType() === 'hospital') {
                $paymentHospitalization += $serviceRow->getPrice() !== null ? (int)$serviceRow->getPrice() : 0;
            } else {
                $paymentNextOrder += $serviceRow->getPrice() !== null ? (int)$serviceRow->getPrice() : 0;
            }
        }

        $calling->setPrice($price);
        $calling->setPaymentHospitalization($paymentHospitalization);
        $calling->setPaymentNextOrder($paymentNextOrder);

        $replay = '';
        $hospital = '';

        /** @var Row $serviceRow */
        foreach ($calling->getServices() as $serviceRow) {
            if ($serviceRow->isStationary()) {
                $hospital .= 'Стационар ' . PHP_EOL;
                $hospital .= $serviceRow->getPlannedPrice() ? 'Ориентировочная цена ' . $serviceRow->getPlannedPrice() . PHP_EOL : '';
                $hospital .= $serviceRow->getPrice() ? 'Предоплата ' . $serviceRow->getPrice() . PHP_EOL : '';
                $hospital .= $serviceRow->getPlannedAt() ? 'Дата ' . $serviceRow->getPlannedAt()->format('d.m.y H:m') . PHP_EOL : '';
                $hospital .= $serviceRow->getDescription() ?
                    'Комментарий ' . $serviceRow->getDescription() . PHP_EOL : '';
            }
            if ($serviceRow->getService()->getType() === 'replay') {
                $replay .= 'Повтор ' . PHP_EOL;
                $replay .= $serviceRow->getPlannedPrice() ? 'Ориентировочная цена ' . $serviceRow->getPlannedPrice() . PHP_EOL : '';
                $replay .= $serviceRow->getPrice() ? 'Предоплата ' . $serviceRow->getPrice() . PHP_EOL : '';
                $replay .= $serviceRow->getPlannedAt() ? '*ПОВТОР* Дата ' . $serviceRow->getPlannedAt()->format('d.m.y H:m') . PHP_EOL : '';
                $replay .= $serviceRow->getDescription() ?
                    'Комментарий ' . $serviceRow->getDescription() . PHP_EOL : '';
                $this->repeat($calling, $serviceRow);

                $this->sender->sendToAdmin(
                    $calling,
                    'Вызов N ' . $calling->getNumberCalling(),
                    'Оформлен повтор'
                );
            }
        }

        $this->completeCall($calling, $hospital, $replay);

        $this->sender->sendToAdmin(
            $calling,
            'Вызов N ' . $calling->getNumberCalling() . ' завершен',
            'Спасибо за работу!'
        );

        $this->wsClient->sendUpdateOffer($calling->getId());

        try {
            $this->buhClient->send($calling);
        }catch (Exception $e) {

        }


        try {
            $this->asteriskDeleteHandler->handle(
                new \App\Asterisk\UseCase\Channel\DeleteByClientPhone\Command($calling->getClient()?->getPhone())
            );

            if ($calling->getClient()?->getPhone()) {
                $this->mcnBlacklistService->deleteFromBlacklist($calling->getClient()->getPhone());
            }
        } catch (Exception) {
        }

        return $this->json([
            'id' => $calling->getId(),
            'title' => $calling->getTitle(),
            'name' => $calling->getName(),
            'phone' => '+70000000000',
            'fio' => $calling->getFio(),
            'numberCalling' => $calling->getNumberCalling(),
            'address' => $calling->getAddress(),
            'status' => $calling->getStatus(),
            'description' => $calling->getDescription(),
            'chronicDiseases' => $calling->getChronicDiseases(),
            'nosology' => $calling->getNosology(),
            'age' => $calling->getAge(),
            'leadType' => $calling->getLeadType(),
            'partnerName' => $calling->getPartnerName(),
            'sendPhone' => $calling->isSendPhone(),
            'rejectedComment' => $calling->getRejectedComment(),
            'createdAt' => $calling->getCreatedAt()?->format('d.m.Y H:i'),
            'updatedAt' => $calling->getUpdatedAt()?->format('d.m.Y H:i'),
            'acceptedAt' => $calling->getAcceptedAt()?->format('d.m.Y H:i'),
            'dispatchedAt' => $calling->getDispatchedAt()?->format('d.m.Y H:i'),
            'arrivedAt' => $calling->getArrivedAt()?->format('d.m.Y H:i'),
            'completedAt' => $calling->getCompletedAt()?->format('d.m.Y H:i'),
            'dateTime' => $calling->getDateTime(),
            'admin' => $calling?->getAdmin() ?: [
                'id' => $calling->getAdmin()->getId(),
                'phone' => $calling->getAdmin()->getPhone(),
                'name' => $calling->getAdmin()->getName(),
            ],
            'doctor' => $calling?->getDoctor() ?: [
                'id' => $calling->getDoctor()->getId(),
                'phone' => $calling->getDoctor()->getPhone(),
                'name' => $calling->getDoctor()->getName(),
            ],
            'price' => $calling->getPrice(),
            'estimated' => $calling->GetEstimated(),
            'prepayment' => $calling->getPrepayment(),
            'note' => $calling->getNote(),
            'passport' => $calling->getPassport(),
            'coastHospitalAdmission' => $calling->getCoastHospitalAdmission(),
            'coastHospital' => $calling->getCoastHospital(),
            'costDay' => $calling->getCostDay(),
            'phoneRelatives' => $calling->getPhoneRelatives(),
            'resultDate' => $calling->getResultDate(),
            'resultTime' => $calling->getResultTime(),
            'partner' => $calling->getPartner() ?: [
                'id' => $calling->getPartner()->getId(),
                'name' => $calling->getPartner()->getName(),
                'whatsappGroup' => $calling->getPartner()->getWhatsappGroup(),
            ],
            'lon' => $calling->getLon(),
            'lat' => $calling->getLat(),
            'services' => [],
            'amount' => $calling->getAmount(),
            'paymentNextOrder' => $calling->getPaymentNextOrder(),
            'paymentHospitalization' => $calling->getPaymentHospitalization(),
            'totalAmount' => $calling->getTotalAmount(),
            'mkadDistance' => $calling->getMkadDistance(),
            'ownerExternalId' => $calling->getOwnerExternalId(),
            'operator' => null,
            'client' => $calling->getClient() ?: [
                'id' => $calling->getClient()->getId(),
                'phone' => $calling->getClient()->getPhone(),
                'name' => $calling->getClient()->getName(),
            ],
            'noBusinessCards' => $calling->isCurrentNoBusinessCards(),
            'partnerHospitalization' => $calling->isCurrentPartnerHospitalization(),
            'images' => [],
            'addressInfo' => $calling->getAddressInfo(),
            'team' => $calling->getTeam() ?: [
                'id' => $calling->getTeam()->getId(),
                'status' => $calling->getTeam()->getStatus(),
                'phone' => $calling->getTeam()->getPhone() ?: [
                    'id' => $calling->getTeam()->getPhone()->getId(),
                    'externalId' => $calling->getTeam()->getPhone()->getExternalId(),
                ],
            ],
            'finishedAt' => $calling->getFinishedAt()?->format('d.m.Y H:i'),
            'repeat' => $calling->getCountRepeat(),
            'statusLabel' => $calling->getStatusLabel(),
        ], Response::HTTP_ACCEPTED);
    }

    private function completeCall(Calling $calling, string $hospital, string $replay): void
    {
        $filter = new LeadsFilter();
        $filter->setIds([$calling->getNumberCalling()]);

        $leads = $this->client->leads()->get($filter);

        if (!$leads) {
            throw new NotFoundHttpException('Не найден лид №' . $calling->getNumberCalling() . ' в AmoCRM');
        }

        $price = 0;
        /** @var Row $serviceRow */
        foreach ($calling->getServices() as $serviceRow) {
            if ($serviceRow->getService()->getType() === 'default' && !$serviceRow->isHospital()) {
                $price += $serviceRow->getPrice() !== null ? (int)$serviceRow->getPrice() : 0;
            }
        }

        $message = 'Информация от бригады:' . PHP_EOL;

        $message .= $calling->getPrice() ? 'Итого: ' . $price . PHP_EOL : '';
        $message .= $calling->getOriginalPhone() ? 'Номер телефона заказчика: ' . $calling->getOriginalPhone() . PHP_EOL : '';
        $message .= $calling->getFio() ? 'ФИО пациента: ' . $calling->getFio() . PHP_EOL : '';
        $message .= $calling->getAge() ? 'Возраст пациента: ' . $calling->getAge() . PHP_EOL : '';
        $message .= $calling->getMkadDistance() ? 'Расстояние до МКАД: ' . $calling->getMkadDistance() . PHP_EOL : '';
        $message .= $calling->getAddress() ? 'Адрес: ' . $calling->getAddress() . PHP_EOL : '';
        $message .= PHP_EOL;

        /** @var Row $serviceRow */
        foreach ($calling->getServices() as $serviceRow) {
            if ($serviceRow->getService()->getType() === 'replay') {
                $message .= 'Повтор' . ($serviceRow->getPlannedAt() ? ' - ' . $serviceRow->getPlannedAt()->format('d.m.y H:m') : '') . PHP_EOL;
                $message .= $serviceRow->getPrice() ? 'Предоплата ' . $serviceRow->getPrice() . PHP_EOL : '';
                $message .= $serviceRow->getDescription() ?
                    'Комментарий: ' . $serviceRow->getDescription() . PHP_EOL : '';
                $message .= PHP_EOL;
            }
        }

        /** @var Row $serviceRow */
        foreach ($calling->getServices() as $serviceRow) {
            if ($serviceRow->isStationary()) {
                continue;
            }
            if ($serviceRow->isHospital()) {
                $message .= 'Госпитализация' . ($serviceRow->getPrice() ? ' - ' . $serviceRow->getPrice() : '') . PHP_EOL;
                $message .= $serviceRow->getDescription() ?
                    'Комментарий: ' . $serviceRow->getDescription() . PHP_EOL : '';
                $message .= PHP_EOL;
            } elseif ($serviceRow->getService()->getType() === 'replay') {
                continue;
            } else {
                $message .= $serviceRow->getService()->getName() . ($serviceRow->getPrice() ? ' - ' . $serviceRow->getPrice() : '') . PHP_EOL;
                $message .= $serviceRow->getDescription() ?
                    'Комментарий: ' . $serviceRow->getDescription() . PHP_EOL : '';
                $message .= PHP_EOL;
            }
        }

        /** @var Row $serviceRow */
        foreach ($calling->getServices() as $serviceRow) {
            if ($serviceRow->isStationary()) {
                $message .= 'Стационар' . ($serviceRow->getPrice() ? ' - ' . $serviceRow->getPrice() : '') . PHP_EOL;
                $message .= $serviceRow->getClinic() ? $serviceRow->getClinic()->getName() . PHP_EOL : '';
                $message .= $serviceRow->getDescription() ?
                    'Комментарий: ' . $serviceRow->getDescription() . PHP_EOL : '';
                $message .= PHP_EOL;
            }
        }

        $message .= 'ВСЕГО:' . $calling->getTotalAmount() . PHP_EOL;

        $message .= PHP_EOL;

        $message .= 'Заявка №' . $calling->getNumberCalling() . PHP_EOL;

        $currentDate = new DateTimeImmutable('now', new DateTimeZone('Europe/Moscow'));

        $entityId = null;

        /** @var LeadModel $lead */
        foreach ($leads as $lead) {
            $entityId = $lead->getId();
            $lead->setStatusId(45084664);
            $lead->setName($currentDate->format('d.m.y') . ' ' . $calling->getFio());
            $lead->setPrice($calling->getPrice());
        }

        $this->client->leads()->update($leads);

        $notesCollection = new NotesCollection();
        $messageNote = new CommonNote();
        $messageNote->setEntityId($entityId)
            ->setText($message)
            ->setCreatedBy(0);

        $notesCollection->add($messageNote);

        try {
            $leadNotesService = $this->client->notes(EntityTypesInterface::LEADS);
            $leadNotesService->add($notesCollection);
        } catch (AmoCRMApiException $e) {
        }

        $calling->setComplete(new DateTimeImmutable());

        $this->partnerReward->calculate($calling);
        $this->operatorReward->calculate($calling);

        $this->flusher->flush();
    }

    private function repeat(Calling $calling, Row $row): void
    {
        $lead = $this->client->leads()->getOne($calling->getNumberCalling());

        if (!$lead) {
            throw new NotFoundHttpException('Не получен лид');
        }

        $linksService = $this->client->links(EntityTypesInterface::LEADS);

        $filter = new EntitiesLinksFilter([$calling->getNumberCalling()]);
        $allLinks = $linksService->get($filter);

        $contactId = null;
        $companyId = null;
        /** @var LinkModel $link */
        foreach ($allLinks as $link) {
            if (
                $link->getMetadata()
                && isset($link->getMetadata()['main_contact'])
                && $link->getMetadata()['main_contact']
            ) {
                $contactId = $link->getToEntityId();
            }

            if ($link->getToEntityType() === 'companies') {
                $companyId = $link->getToEntityId();
            }
        }

        if (!$contactId) {
            throw new NotFoundHttpException('Не найден контакт при создании повтора');
        }

        $name = $row->getPlannedAt()->format('d.m.y ') . ' ПОВТОР в ' . $row->getPlannedAt()->format('H:i ') . ' ' . $calling->getFio();

        $customFieldsValues = new CustomFieldsValuesCollection();
        foreach ($lead->getCustomFieldsValues() as $customFieldsValue) {
            // Время прибытия, бригаду, админа и врача не переносим в повотор
            if (
                $customFieldsValue->getFieldId() === 880453 ||
                $customFieldsValue->getFieldId() === 875863 ||
                $customFieldsValue->getFieldId() === 873879 ||
                $customFieldsValue->getFieldId() === 873881
            ) {
                continue;
            }
            $customFieldsValues->add($customFieldsValue);
        }

        if ($row->getPlannedAt()) {
            $textCustomFieldValueModel = new DateTimeCustomFieldValuesModel();
            $textCustomFieldValueModel->setFieldId(880453);
            $textCustomFieldValueModel->setValues(
                (
                new DateTimeCustomFieldValueCollection())
                    ->add(
                        (new DateTimeCustomFieldValueModel())
                            ->setValue($row->getPlannedAt()->getTimestamp())
                    )
            );
            $customFieldsValues->add($textCustomFieldValueModel);
        }

        $newLead = new LeadModel();
        $newLead->setName($name)
            ->setCreatedBy(0)
            ->setPrice($calling->getEstimated())
            ->setStatusId(38307805)
            ->setPipelineId(4018768)
            ->setResponsibleUserId($lead->getResponsibleUserId())
            ->setCustomFieldsValues($customFieldsValues)
            ->setContacts(
                (new ContactsCollection())
                    ->add(
                        (new ContactModel())
                            ->setId($contactId)
                            ->setIsMain(true)
                    )
            );

        if ($companyId) {
            $newLead->setCompany(
                (new CompanyModel())
                    ->setId($companyId)
            );
        }

        $leadsCollection = new LeadsCollection();
        $leadsCollection->add($newLead);

        $leadModel = $this->client->leads()->addOne($newLead);

        $calling->setOwnerExternalId((string)$leadModel->getId());

        $this->flusher->flush();

        $command = new Command((string)$leadModel->getId());
        $this->handler->handle($command);
    }
}
