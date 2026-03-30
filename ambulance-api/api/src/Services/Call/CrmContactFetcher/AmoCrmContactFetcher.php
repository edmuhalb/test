<?php

declare(strict_types=1);

namespace App\Services\Call\CrmContactFetcher;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Models\CustomFieldsValues\MultitextCustomFieldValuesModel;
use AmoCRM\Models\LeadModel;
use App\Services\AmoCRM;
use App\UseCase\Call\SendFromCrm\Contact;
use DomainException;
use Exception;

class AmoCrmContactFetcher implements CrmContactFetcherInterface
{
    private AmoCRMApiClient $client;

    public function __construct(
        AmoCRM $amoCRM,
    ) {
        $this->client = $amoCRM->getClient();
    }

    public function fetch(int $leadId): Contact
    {
        $lead = $this->client->leads()->getOne($leadId, [LeadModel::CONTACTS]);

        if (!$lead->getMainContact()->getId()) {
            throw new DomainException('Не указан контакт');
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
        $phone = null;

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

        if (!$phone) {
            throw new DomainException('Не найден телефон');
        }

        return new Contact(
            $name,
            $phone
        );
    }
}
