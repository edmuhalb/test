<?php

declare(strict_types=1);

namespace App\Serializer\Call;

use App\UseCase\Call\SendFromCrm\Lead;
use DomainException;

class AmoCrmToAppDenormalizer implements CrmToAppDenormalizerInterface
{
    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): Lead
    {
        if (isset($data['leads']['update'][0])) {
            $lead = $data['leads']['update'][0];
        } elseif (isset($data['leads']['add'][0])) {
            $lead = $data['leads']['add'][0];
        } else {
            throw new DomainException('Нет данных в теле запроса');
        }

        $leadDto = new Lead(
            (int)$lead['id'],
            (int)$lead['status_id'],
            (int)$lead['pipeline_id'],
            $lead['name'],
        );

        if (\array_key_exists('responsible_user_id', $lead) && null !== $lead['responsible_user_id']) {
            $leadDto->setOperatorId((int)$lead['responsible_user_id']);
        }

        if (!empty($lead['custom_fields'])) {
            foreach ($lead['custom_fields'] as $field) {
                if ((int)$field['id'] === 880453) {
                    $leadDto->dateTime = $field['values'][0] ?? null;
                }
                if ((int)$field['id'] === 870903) {
                    $leadDto->address = $field['values'][0]['value'] ?? null;
                }

                if ((int)$field['id'] === 968865) {
                    $leadDto->addressInfo = $field['values'][0]['value'] ?? null;
                }

                if ((int)$field['id'] === 875863) {
                    $leadDto->team = $field['values'][0]['value'] ?? null;
                }
                if ((int)$field['id'] === 880527) {
                    $leadDto->nosology = $field['values'][0]['value'] ?? null;
                }

                if ((int)$field['id'] === 870907) {
                    $leadDto->age = $field['values'][0]['value'] ?? null;
                }

                if ((int)$field['id'] === 870945) {
                    $leadDto->description = $field['values'][0]['value'] ?? '';
                }

                if ((int)$field['id'] === 884333) {
                    $leadDto->hz = $field['values'][0]['value'] ?? null;
                }

                if ((int)$field['id'] === 960101) {
                    $leadDto->leadType = $field['values'][0]['value'] ?? null;
                }

                if ((int)$field['id'] === 882361) {
                    $leadDto->partnerExternalId = $field['values'][0]['enum'] ?? null;
                    $leadDto->partnerName = $field['values'][0]['value'] ?? null;
                }
                if ((int)$field['id'] === 896921) {
                    $leadDto->sendPhone = $field['values'][0]['value'] ?? false;
                }

                if ((int)$field['id'] === 968691) {
                    $leadDto->partnerHospitalization = $field['values'][0]['value'] ?? false;
                }

                if ((int)$field['id'] === 968867) {
                    $leadDto->noBusinessCards = $field['values'][0]['value'] ?? false;
                }
            }
        }

        return $leadDto;
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null): bool
    {
        return \is_array($data) && is_a($type, Lead::class, true);
    }
}
