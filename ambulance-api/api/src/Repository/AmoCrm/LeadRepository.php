<?php

declare(strict_types=1);

namespace App\Repository\AmoCrm;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Collections\ContactsCollection;
use AmoCRM\Collections\CustomFieldsValuesCollection;
use AmoCRM\Collections\Leads\LeadsCollection;
use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Models\ContactModel;
use AmoCRM\Models\CustomFieldsValues\SelectCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\TextCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\SelectCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\TextCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueModels\SelectCustomFieldValueModel;
use AmoCRM\Models\CustomFieldsValues\ValueModels\TextCustomFieldValueModel;
use AmoCRM\Models\LeadModel;
use App\Services\AmoCRM;
use DomainException;

class LeadRepository
{
    private AmoCRMApiClient $client;

    public function __construct(AmoCRM $amoCRM)
    {
        $this->client = $amoCRM->getClient();
    }

    public function create(string $partnerId, string $partnerName, int $contactId, string $description): LeadModel
    {
        $lead = new LeadModel();
        $lead->setName('Сделка создана из ЛК партнера ' . $partnerName)
            ->setContacts(
                (new ContactsCollection())
                    ->add(
                        (new ContactModel())
                            ->setId($contactId)
                    )
            );

        $leadCustomFieldsValues = new CustomFieldsValuesCollection();
        $textCustomFieldValueModel = new TextCustomFieldValuesModel();
        $textCustomFieldValueModel->setFieldId(870945);
        $textCustomFieldValueModel->setValues(
            (new TextCustomFieldValueCollection())
                ->add((new TextCustomFieldValueModel())->setValue($description))
        );
        $leadCustomFieldsValues->add($textCustomFieldValueModel);

        $teamSelectCustomValueModel = new SelectCustomFieldValuesModel();
        $teamSelectCustomValueModel->setFieldId(882361);
        $teamSelectCustomValueModel->setValues(
            (new SelectCustomFieldValueCollection())
                ->add(
                    (new SelectCustomFieldValueModel())
                        ->setValue($partnerName)
                        ->setEnumId((int)$partnerId)
                )
        );
        $leadCustomFieldsValues->add($teamSelectCustomValueModel);

        $lead->setCustomFieldsValues($leadCustomFieldsValues);

        $lead->setPipelineId(4108468);
        $lead->setStatusId(38816524);

        $leadsCollection = new LeadsCollection();
        $leadsCollection->add($lead);

        try {
            $leadsCollection = $this->client->leads()->add($leadsCollection);
            return $leadsCollection->first();
        } catch (AmoCRMApiException $e) {
            throw new DomainException('Не удалось создать сделку: ' . $e->getMessage());
        }
    }
}
