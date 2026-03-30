<?php

declare(strict_types=1);

namespace App\UseCase\Call\SetTime;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Exceptions\AmoCRMMissedTokenException;
use AmoCRM\Exceptions\AmoCRMoAuthApiException;
use AmoCRM\Filters\LeadsFilter;
use AmoCRM\Models\LeadModel;
use App\Flusher;
use App\Repository\CallingRepository;
use App\Services\AmoCRM;
use Carbon\Carbon;
use DateTimeImmutable;
use Exception;

class Handler
{
    private AmoCRMApiClient $client;

    public function __construct(
        private readonly CallingRepository $calls,
        private readonly Flusher $flusher,
        AmoCRM $amoCRM,
    ) {
        $this->client = $amoCRM->getClient();
    }

    /**
     * @throws AmoCRMApiException
     * @throws AmoCRMoAuthApiException
     * @throws AmoCRMMissedTokenException
     * @throws Exception
     */
    public function handle(Command $command): void
    {
        $call = $this->calls->getById($command->id);
        $dateTime = new DateTimeImmutable($command->time);

        $call->setDateTime($dateTime);

        $filter = new LeadsFilter();
        $filter->setIds([$call->getNumberCalling()]);

        $leads = $this->client->leads()->get($filter);

        /** @var LeadModel $lead */
        foreach ($leads as $lead) {
            $customFields = $lead->getCustomFieldsValues();
            foreach ($customFields as $customFieldValues) {
                if ($customFieldValues->getFieldId()  === 880453) {
                    $customFieldValue = $customFieldValues->getValues()->first();
                    /** @var Carbon|null $value */
                    $value = $customFieldValue->getValue();
                    if ($value) {
                        $customFieldValue->setValue(Carbon::createFromFormat('Y-m-d H:i', $command->time));
                    }
                }
            }
        }

        $this->client->leads()->update($leads);
        $this->flusher->flush();
    }
}
