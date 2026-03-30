<?php

declare(strict_types=1);

namespace App\Services;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Collections\ContactsCollection;
use AmoCRM\Collections\CustomFieldsValuesCollection;
use AmoCRM\Collections\Leads\LeadsCollection;
use AmoCRM\Filters\EntitiesLinksFilter;
use AmoCRM\Helpers\EntityTypesInterface;
use AmoCRM\Models\CompanyModel;
use AmoCRM\Models\ContactModel;
use AmoCRM\Models\LeadModel;
use AmoCRM\Models\LinkModel;
use App\Entity\Calling\Calling;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class HospitalizationScheduler
{
    private AmoCRMApiClient $client;

    public function __construct(
        AmoCRM $amoCRM,
    ) {
        $this->client = $amoCRM->getClient();
    }

    public function schedule(Calling $calling): void
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
            throw new NotFoundHttpException('Не найден контакт при создании госпитализации');
        }

        $customFieldsValues = new CustomFieldsValuesCollection();
        foreach ($lead->getCustomFieldsValues() as $customFieldsValue) {
            // бригаду, админа и врача не переносим в повотор
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

        $this->client->leads()->add($leadsCollection);
    }
}
