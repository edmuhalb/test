<?php

declare(strict_types=1);

namespace App\State\Call;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Collections\ContactsCollection;
use AmoCRM\Collections\CustomFieldsValuesCollection;
use AmoCRM\Collections\Leads\LeadsCollection;
use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Exceptions\AmoCRMMissedTokenException;
use AmoCRM\Exceptions\AmoCRMoAuthApiException;
use AmoCRM\Exceptions\InvalidArgumentException;
use AmoCRM\Filters\EntitiesLinksFilter;
use AmoCRM\Helpers\EntityTypesInterface;
use AmoCRM\Models\CompanyModel;
use AmoCRM\Models\ContactModel;
use AmoCRM\Models\LeadModel;
use AmoCRM\Models\LinkModel;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Calling\Calling;
use App\Entity\Calling\Status;
use App\Entity\Hospital\Clinic;
use App\Entity\Hospital\Hospital;
use App\Entity\MediaObject;
use App\Repository\Hospital\HospitalRepository;
use App\Services\AmoCRM;
use App\Services\Call\PartnerReward;
use App\Services\CallingSender;
use App\Services\File\UploadedBase64File;
use App\Services\Payroll\CallPayrollCalculator;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PostProcessor implements ProcessorInterface
{
    private AmoCRMApiClient $client;

    public function __construct(
        private readonly PartnerReward $partnerReward,
        AmoCRM $amoCRM,
        private readonly CallingSender $sender,
        private readonly HospitalRepository $hospitals,
        private readonly ProcessorInterface $processor,
        private readonly CallPayrollCalculator $employeePayrollCalculator,
    ) {
        $this->client = $amoCRM->getClient();
    }

    /**
     * @throws AmoCRMApiException
     * @throws AmoCRMMissedTokenException
     * @throws AmoCRMoAuthApiException
     * @throws InvalidArgumentException
     * @throws NonUniqueResultException
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        /** @var Calling $data */
        $images = $data->getImages();

        /** @var MediaObject $image */
        foreach ($images as $image) {
            if ($image->base64content) {
                $imageFile = new UploadedBase64File($image->base64content, 'call_image.png');
                $image->file = $imageFile;
            }
        }

        $hospital = $this->hospitals->findOneByOwnerId($data->getId());

        $this->checkStationary($data, $hospital);

        if ($data->getStatus() === Status::COMPLETED) {
            $this->partnerReward->calculate($data);
            try {
                $this->employeePayrollCalculator->calculate($data);
            } catch (Exception) {
            }
        }

        return $this->processor->process($data, $operation, $uriVariables, $context);
    }

    /**
     * @throws AmoCRMoAuthApiException
     * @throws InvalidArgumentException
     * @throws AmoCRMApiException
     * @throws AmoCRMMissedTokenException
     * @throws NonUniqueResultException
     */
    private function checkStationary(Calling $call, ?Hospital $hospital): void
    {
        foreach ($call->getServices() as $row) {
            if ($row->isStationary()) {
                $price = $row->getPrice() !== null ? (int)$row->getPrice() : null;
                if (!$hospital) {
                    $leadModel = $this->createStationaryLead($call);

                    $this->createStationary(
                        $call,
                        $row->getClinic(),
                        $price,
                        (string)$leadModel->getId()
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

    /**
     * @throws NonUniqueResultException
     */
    private function createStationary(Calling $calling, ?Clinic $clinic, ?int $price, ?string $externalId): void
    {
        if (!$externalId) {
            return;
        }

        if (!$this->hospitals->findOneByExternal($externalId)) {
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
        }

        $this->sender->sendToAdmin(
            $calling,
            'Вызов N ' . $calling->getNumberCalling(),
            'Создано назначение на стационар'
        );
    }

    /**
     * @throws AmoCRMoAuthApiException
     * @throws InvalidArgumentException
     * @throws AmoCRMApiException
     * @throws AmoCRMMissedTokenException
     */
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
}
