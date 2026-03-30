<?php

declare(strict_types=1);

namespace App\UseCase\Call\SetResponsiblePerson;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Models\LeadModel;
use App\Flusher;
use App\Repository\CallingRepository;
use App\Services\AmoCRM;
use DomainException;

class Handler
{
    private AmoCRMApiClient $client;

    public function __construct(
        AmoCRM $amoCRM,
        private readonly CallingRepository $calls,
        private readonly Flusher $flusher,
    ) {
        $this->client = $amoCRM->getClient();
    }

    public function handle(Command $command): void
    {
        try {
            $lead = $this->client->leads()->getOne($command->externalId, [LeadModel::CONTACTS]);
            if (!$lead) {
                throw new DomainException('Не найден лид');
            }
            $user = $this->client->users()->getOne($lead->getResponsibleUserId());
            if (!$user) {
                throw new DomainException('Не найден ответственный пользователь лида');
            }
        } catch (AmoCRMApiException $e) {
            throw new DomainException('Не удалось получить лид id' . $command->externalId . ' ' . $e->getMessage());
        }

        $call = $this->calls->findOneByNumber($command->externalId);

        $call->setResponsibleUserId($user->getId());
        $call->setResponsibleUserName($user->getName());

        $this->flusher->flush();
    }
}
