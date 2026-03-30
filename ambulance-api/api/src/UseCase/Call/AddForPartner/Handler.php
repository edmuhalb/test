<?php

declare(strict_types=1);

namespace App\UseCase\Call\AddForPartner;

use App\Entity\Calling\Calling;
use App\Entity\Calling\Status;
use App\Flusher;
use App\Repository\AmoCrm\ContactRepository;
use App\Repository\AmoCrm\LeadRepository;
use App\Repository\CallingRepository;
use App\Repository\PartnerRepository;
use DateTimeImmutable;
use DomainException;

class Handler
{
    public function __construct(
        private readonly ContactRepository $contacts,
        private readonly LeadRepository $leads,
        private readonly PartnerRepository $partners,
        private readonly CallingRepository $calls,
        private readonly Flusher $flusher
    ) {}

    public function handle(Command $command): void
    {
        $partner = $this->partners->getById($command->partnerId);
        $contactId = $this->contacts->findByPhone($command->phone);
        if (!$contactId) {
            $contactId = $this->contacts->createByPhone($command->phone);
        }

        $lead = $this->leads->create(
            $partner->getExternalId(),
            $partner->getName(),
            $contactId,
            $command->description
        );

        $call = $this->calls->findOneByOwnerExternalId((string)$lead->getId());
        if ($call) {
            throw new DomainException('Вызов уже существует');
        }

        $clientPhone = preg_replace('/[^0-9]/', '', $command->phone);
        $phone = '+' . $clientPhone;

        $call = new Calling(
            (string)$lead->getId(),
            $lead->getName(),
            $phone,
            $phone
        );

        $call->setDateTime(new DateTimeImmutable());
        $call->setUpdatedAt(new DateTimeImmutable());
        $call->setPartner($partner);
        $call->setStatus(Status::notReady());

        $this->calls->add($call);

        $this->flusher->flush();
    }
}
