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
use App\Asterisk\UseCase\Channel as AsteriskChannel;
use App\Entity\Calling\Calling;
use App\Entity\Calling\Row;
use App\Entity\Calling\Status;
use App\Entity\Hospital\Clinic;
use App\Entity\Hospital\Hospital;
use App\Entity\MediaObject;
use App\Entity\User\User;
use App\EventListener\CallPreDenormalizeListener;
use App\Flusher;
use App\Repository\Hospital\HospitalRepository;
use App\Services\AmoCRM;
use App\Services\ATS\BlacklistService\McnBlacklistService;
use App\Services\BuhClient;
use App\Services\Call\OperatorReward;
use App\Services\Call\PartnerReward;
use App\Services\CallingSender;
use App\Services\Payroll\CallPayrollCalculator;
use App\Services\WSClient;
use App\UseCase\Call\AddOrUpdateRepeat\Command;
use App\UseCase\Call\AddOrUpdateRepeat\Handler;
use DateTimeImmutable;
use DateTimeZone;
use DomainException;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[AsController]
class PatchAction extends AbstractController
{
    private AmoCRMApiClient $client;

    public function __construct(
        private readonly CallPreDenormalizeListener                  $callPreDenormalizeListener,
        AmoCRM                                                       $amoCRM,
        private readonly WSClient                                    $wsClient,
        private readonly AsteriskChannel\AddOrUpdate\Handler         $asteriskAddOrUpdateHandler,
        private readonly AsteriskChannel\DeleteByClientPhone\Handler $asteriskDeleteHandler,
        private readonly McnBlacklistService                         $mcnBlacklistService,
        private readonly CallingSender                               $sender,
        private readonly Flusher                                     $flusher,
        private readonly PartnerReward                               $partnerReward,
        private readonly OperatorReward                              $operatorReward,
        private readonly CallPayrollCalculator                       $employeePayrollCalculator,
        private readonly Handler                                     $handler,
        private readonly HospitalRepository                          $hospitals,
        private readonly BuhClient                                   $buhClient,
    )
    {
        $this->client = $amoCRM->getClient();
    }

    public function __invoke(Calling $calling): JsonResponse
    {
        $this->handleServicesChange($calling);

        $this->handleStatusChange($calling);

        try {
            $this->buhClient->send($calling);
        } catch (Exception $e) {

        }

        $this->flusher->flush();

        return $this->json(
            $calling,
            200,
            [],
            [
                'groups' => [
                    'v1-call:read',
                    'client:item:read',
                ],
            ]
        );
    }

    public function handleStatusChange(Calling $calling): void
    {
        $newStatus = $calling->getStatus();
        $originalStatus = $this->callPreDenormalizeListener->getStatus();
        if ($newStatus === $originalStatus) {
            return;
        }

        if ($newStatus === Status::ACCEPTED) {
            $this->handleAcceptedStatus($calling);
        }

        if ($newStatus === Status::ARRIVED) {
            $this->handleArrivedStatus($calling);
        }

        if ($newStatus === Status::DISPATCHED) {
            $this->handleDispatchedStatus($calling);
        }

        if ($newStatus === Status::TREATING) {
            $this->handleTreatingStatus($calling);
        }

        if ($newStatus === Status::COMPLETED) {
            $this->handleCompletedStatus($calling);
        }

        if ($newStatus === Status::REJECTED) {
            if ($calling->getReasonForCancellation()?->isReassignmentRequired()) {
                $this->handleReassignment($calling);
            } else {
                $this->handleRejectedStatus($calling);
            }
        }

    }

    public function handleAcceptedStatus(Calling $calling): void
    {
        /** @var User $user */
        $user = $this->getUser();

        if ($calling->getAdmin()?->getId() !== $user->getId()) {
            throw new DomainException('Принять вызов может только администратор');
        }

        $this->updateAmoCRMLeads($calling, 62358394);

        $calling->setAccepted(new DateTimeImmutable());

        $this->wsClient->sendUpdateOffer($calling->getId());

        $this->handleAsteriskUpdate($calling);
    }

    public function updateAmoCRMLeads(Calling $calling, int $leadStatusId): void
    {
        if ($calling->isBuh()) {
            return;
        }

        $filter = new LeadsFilter();
        $filter->setIds([$calling->getNumberCalling()]);

        $leads = $this->client->leads()->get($filter);

        /** @var LeadModel $lead */
        foreach ($leads as $lead) {
            $lead->setStatusId($leadStatusId);
        }

        $this->client->leads()->update($leads);
    }

    public function handleAsteriskUpdate(Calling $calling): void
    {
        try {
            $this->asteriskAddOrUpdateHandler->handle(
                new AsteriskChannel\AddOrUpdate\Command(
                    $calling->getClient()?->getPhone(),
                    $calling->getAdmin()?->getPhone()
                )
            );

            if ($calling->getClient()?->getPhone()) {
                $this->mcnBlacklistService->addToBlacklist($calling->getClient()->getPhone());
            }
        } catch (Exception) {
        }
    }

    public function handleAsteriskDelete(Calling $calling): void
    {
        try {
            $this->asteriskDeleteHandler->handle(
                new AsteriskChannel\DeleteByClientPhone\Command($calling->getClient()?->getPhone())
            );

            if ($calling->getClient()?->getPhone()) {
                $this->mcnBlacklistService->deleteFromBlacklist($calling->getClient()->getPhone());
            }
        } catch (Exception) {
        }
    }

    private function handleArrivedStatus(Calling $calling): void
    {
        $this->updateAmoCRMLeads($calling, 62358398);

        $calling->setArrived(new DateTimeImmutable());

        $this->wsClient->sendUpdateOffer($calling->getId());
    }

    private function handleDispatchedStatus(Calling $calling): void
    {
        $this->updateAmoCRMLeads($calling, 38187418);

        $calling->setDispatched(new DateTimeImmutable());

        $this->wsClient->sendUpdateOffer($calling->getId());
    }

    private function handleTreatingStatus(Calling $calling): void
    {
        $this->updateAmoCRMLeads($calling, 38187418);

        $this->wsClient->sendUpdateOffer($calling->getId());
    }

    private function handleCompletedStatus(Calling $calling): void
    {
        $price = $this->calculateTotalPrice($calling);

        $calling->setPrice($price['total']);
        $calling->setPaymentHospitalization($price['hospital']);
        $calling->setPaymentNextOrder($price['nextOrder']);

        if (!$calling->isBuh()) {
            /** @var Row $serviceRow */
            foreach ($calling->getServices() as $serviceRow) {
                if ($serviceRow->isReplay()) {
                    $this->repeat($calling, $serviceRow);

                    $this->sender->sendToAdmin(
                        $calling,
                        'Вызов N ' . $calling->getNumberCalling(),
                        'Оформлен повтор'
                    );
                }
            }
        }

        $this->completeCall($calling);

        $this->sender->sendToAdmin(
            $calling,
            'Вызов N ' . $calling->getNumberCalling() . ' завершен',
            'Спасибо за работу!'
        );

        $this->wsClient->sendUpdateOffer($calling->getId());

        $this->handleAsteriskDelete($calling);
    }

    private function completeCall(Calling $calling): void
    {
        if (!$calling->isBuh()) {

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
        }

        $calling->setComplete(new DateTimeImmutable());

        $this->partnerReward->calculate($calling);
        $this->operatorReward->calculate($calling);

        try {
            $this->employeePayrollCalculator->calculate($calling);
        } catch (Exception $e) {
        }

        $this->flusher->flush();
    }

    private function repeat(Calling $calling, Row $row): void
    {
        if ($calling->isBuh()) {
            return;
        }
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
            // Время прибытия, бригаду, админа и врача не переносим в повтор
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

    private function calculateTotalPrice(Calling $calling): array
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

        return [
            'total' => $price,
            'hospital' => $paymentHospitalization,
            'nextOrder' => $paymentNextOrder,
        ];
    }

    private function handleServicesChange(Calling $call): void
    {
       // if ($call->isBuh()) {
       //     return;
       // }
        $hospital = $this->hospitals->findOneByOwnerId($call->getId());

        foreach ($call->getServices() as $row) {
            if ($row->isStationary()) {
                $price = $row->getPrice() !== null ? (int)$row->getPrice() : null;
                if (!$hospital) {
                    //$leadModel = $this->createStationaryLead($call);

                    $this->createStationary(
                        $call,
                        $row->getClinic(),
                        $price,
                        //(string)$leadModel->getId()
                    );
                } else {
                    $this->updateStationary(
                        $call,
                        $row->getClinic(),
                        $hospital,
                        $price,
                    );
                }
                return;
            }
        }

        if ($hospital) {
            $this->cancelStationary($call, $hospital);
        }
    }

    private function createStationary(Calling $calling, ?Clinic $clinic, ?int $price, ?string $externalId = null): void
    {
        //if (!$externalId) {
        //    return;
        //}

        //if (!$this->hospitals->findOneByExternal($externalId)) {
            $hospital = new Hospital();
            $hospital->setExternal($externalId);
            $hospital->setStatus('assigned');
            $hospital->setFio($calling->getFio());
            $hospital->setPartner($calling->getPartner());
            $hospital->setPhone($calling->getOriginalPhone());
            $hospital->setOwner($calling);
            $hospital->setPrepayment($price);

            $hospital->setClinic($clinic);

            foreach ($calling->getImages() as $image) {
                $hospital->addImage($image);
            }

            $this->hospitals->save($hospital, true);
        //}

        $this->sender->sendToAdmin(
            $calling,
            'Вызов N ' . $calling->getNumberCalling(),
            'Создано назначение на стационар'
        );
    }

    private function createStationaryLead(Calling $calling): LeadModel
    {
        $lead = $this->client->leads()->getOne((int)$calling->getNumberCalling());

        if (!$lead) {
            throw new NotFoundHttpException('Не получен лид');
        }

        $linksService = $this->client->links(EntityTypesInterface::LEADS);

        $filter = new EntitiesLinksFilter([(int)$calling->getNumberCalling()]);
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
            throw new NotFoundHttpException('Не найден контакт при создании стационара');
        }

        $customFieldsValues = new CustomFieldsValuesCollection();
        foreach ($lead->getCustomFieldsValues() as $customFieldsValue) {
            if (
                $customFieldsValue->getFieldId() === 875863 ||
                $customFieldsValue->getFieldId() === 873879 ||
                $customFieldsValue->getFieldId() === 873881
            ) {
                continue;
            }
            $customFieldsValues->add($customFieldsValue);
        }

        $newLead = new LeadModel();
        $newLead->setName($lead->getName())
            ->setCreatedBy(0)
            ->setPrice($calling->getCoastHospital())
            ->setStatusId(38709310)
            ->setPipelineId(4093174)
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

        return $this->client->leads()->addOne($newLead);
    }

    private function updateStationary(Calling $call, ?Clinic $clinic, Hospital $hospital, ?int $price): void
    {
        if ($hospital->getStatus() !== 'assigned' && $hospital->getStatus() !== 'cancelled') {
            return;
        }

        $changedImages = false;

        if ($hospital->getImages()->count() !== $call->getImages()->count()) {
            $changedImages = true;
        }

        /** @var MediaObject $image */
        foreach ($call->getImages() as $image) {
            if (!$hospital->getImages()->contains($image)) {
                $changedImages = true;
            }
        }

        if (
            $hospital->getPhone() === $call->getOriginalPhone() &&
            $hospital->getFio() === $call->getFio() &&
            $hospital->getPrepayment() === $price &&
            $hospital->getClinic()?->getId() === $clinic?->getId() &&
            !$changedImages
        ) {
            return;
        }

        $hospital->setFio($call->getFio());
        $hospital->setPhone($call->getOriginalPhone());
        $hospital->setPrepayment($price);
        $hospital->setStatus('assigned');
        $hospital->setClinic($clinic);

        if ($changedImages) {
            $hospital->getImages()->clear();
            /** @var MediaObject $image */
            foreach ($call->getImages() as $image) {
                $hospital->addImage($image);
            }
        }

        $this->hospitals->save($hospital, true);

        $this->sender->sendToAdmin(
            $call,
            'Вызов N ' . $call->getNumberCalling(),
            'Обновлено назначение на стационар'
        );
    }

    private function cancelStationary(Calling $call, Hospital $hospital): void
    {
        if ($hospital->getStatus() !== 'assigned') {
            return;
        }
        $hospital->setStatus('cancelled');

        $this->hospitals->save($hospital, true);

        $this->sender->sendToAdmin(
            $call,
            'Вызов N ' . $call->getNumberCalling(),
            'Отменено назначение на стационар'
        );
    }

    private function handleRejectedStatus(Calling $calling): void
    {
        if (!$calling->isBuh()) {
            $filter = new LeadsFilter();
            $filter->setIds([$calling->getNumberCalling()]);

            $leads = $this->client->leads()->get($filter);

            if (!$leads) {
                throw new NotFoundHttpException('Не найден лид №' . $calling->getNumberCalling() . ' в AmoCRM');
            }

            $message = 'Информация от бригады' . PHP_EOL;
            $message .= 'Отмена заявки №' . $calling->getNumberCalling() . PHP_EOL;
            $message .= $calling->getReasonForCancellation() ? 'Причина отмены ' . $calling->getReasonForCancellation()->getName() . PHP_EOL : '';

            $entityId = null;
            $currentDate = new DateTimeImmutable('now', new DateTimeZone('Europe/Moscow'));
            /** @var LeadModel $lead */
            foreach ($leads as $lead) {
                $entityId = $lead->getId();
                $lead->setStatusId(74882710);
                $lead->setName('Неуспех ' . $currentDate->format('d.m.y') . ' ' . $calling->getName());
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
        }

        $calling->setReject(new DateTimeImmutable(), $calling->getRejectedComment());

        $this->sender->sendToAdmin(
            $calling,
            'Вызов N ' . $calling->getNumberCalling() . ' отменен',
            'Спасибо за информацию!'
        );

        $this->wsClient->sendUpdateOffer($calling->getId());

        $this->handleAsteriskDelete($calling);
    }

    private function handleReassignment(Calling $calling): void
    {
        if (!$calling->isBuh()) {
            $filter = new LeadsFilter();
            $filter->setIds([$calling->getNumberCalling()]);

            $leads = $this->client->leads()->get($filter);

            if (!$leads) {
                throw new NotFoundHttpException('Не найден лид №' . $calling->getNumberCalling() . ' в AmoCRM');
            }

            $message = 'Информация от бригады' . PHP_EOL;
            $message .= 'Отмена заявки №' . $calling->getNumberCalling() . PHP_EOL;
            $message .= $calling->getReasonForCancellation() ? 'Причина отмены ' . $calling->getReasonForCancellation()->getName() . PHP_EOL : '';
            $message .= 'Заявка отправлена на переназначение';

            $entityId = null;
            /** @var LeadModel $lead */
            foreach ($leads as $lead) {
                $entityId = $lead->getId();
                $lead->setStatusId(38307946);
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
        }

        $calling->setStatus(Status::waiting());

        $calling->setTeam(null);
        $calling->setAdmin(null);
        $calling->setDoctor(null);

        $this->sender->sendToAdmin(
            $calling,
            'Вызов N ' . $calling->getNumberCalling() . ' отправлен на переназначение',
            'Спасибо за информацию!'
        );

        $this->wsClient->sendUpdateOffer($calling->getId());
    }
}
