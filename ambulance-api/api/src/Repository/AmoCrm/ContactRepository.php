<?php

declare(strict_types=1);

namespace App\Repository\AmoCrm;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Collections\CustomFieldsValuesCollection;
use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Exceptions\AmoCRMApiNoContentException;
use AmoCRM\Filters\ContactsFilter;
use AmoCRM\Models\ContactModel;
use AmoCRM\Models\CustomFieldsValues\MultitextCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\MultitextCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueModels\MultitextCustomFieldValueModel;
use App\Services\AmoCRM;
use DomainException;
use Exception;

class ContactRepository
{
    private AmoCRMApiClient $client;

    public function __construct(AmoCRM $amoCRM)
    {
        $this->client = $amoCRM->getClient();
    }

    public function findByPhone(string $phone): ?int
    {
        try {
            $contacts = $this->client->contacts()->get(
                (new ContactsFilter())
                    ->setQuery($phone)
            );
            $contact = $contacts->first();
            return $contact->getId();
        } catch (AmoCRMApiNoContentException $e) {
            return null;
        } catch (Exception $e) {
            throw new DomainException($e->getMessage());
        }
    }

    public function createByPhone($phone): int
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        $phone = '+' . $phone;

        $contact = new ContactModel();

        $contact->setCustomFieldsValues(
            (new CustomFieldsValuesCollection())
                ->add(
                    (new MultitextCustomFieldValuesModel())
                        ->setFieldCode('PHONE')
                        ->setValues(
                            (new MultitextCustomFieldValueCollection())
                                ->add(
                                    (new MultitextCustomFieldValueModel())
                                        ->setValue($phone)
                                )
                        )
                )
        );

        $contact->setName($this->phoneFormat($phone));

        try {
            $contactModel = $this->client->contacts()->addOne($contact);
            return $contactModel->getId();
        } catch (AmoCRMApiException $e) {
            throw new DomainException('Не удалось создать контакт ' . $e->getMessage());
        }
    }

    private function phoneFormat($phone)
    {
        $phone = trim($phone);

        return preg_replace(
            [
                '/[\+]?([7|8])[-|\s]?\([-|\s]?(\d{3})[-|\s]?\)[-|\s]?(\d{3})[-|\s]?(\d{2})[-|\s]?(\d{2})/',
                '/[\+]?([7|8])[-|\s]?(\d{3})[-|\s]?(\d{3})[-|\s]?(\d{2})[-|\s]?(\d{2})/',
                '/[\+]?([7|8])[-|\s]?\([-|\s]?(\d{4})[-|\s]?\)[-|\s]?(\d{2})[-|\s]?(\d{2})[-|\s]?(\d{2})/',
                '/[\+]?([7|8])[-|\s]?(\d{4})[-|\s]?(\d{2})[-|\s]?(\d{2})[-|\s]?(\d{2})/',
                '/[\+]?([7|8])[-|\s]?\([-|\s]?(\d{4})[-|\s]?\)[-|\s]?(\d{3})[-|\s]?(\d{3})/',
                '/[\+]?([7|8])[-|\s]?(\d{4})[-|\s]?(\d{3})[-|\s]?(\d{3})/',
            ],
            [
                '+7 ($2) $3-$4-$5',
                '+7 ($2) $3-$4-$5',
                '+7 ($2) $3-$4-$5',
                '+7 ($2) $3-$4-$5',
                '+7 ($2) $3-$4',
                '+7 ($2) $3-$4',
            ],
            $phone
        );
    }
}
