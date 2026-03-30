<?php

declare(strict_types=1);

namespace App\UseCase\Call\SendFromCrm;

use App\Entity\Calling\Status;
use App\Flusher;
use App\Repository\CallingRepository;
use App\Repository\ClientRepository;
use App\Repository\PartnerRepository;
use App\Services\Call\DistanceDeterminant;
use App\Services\Call\TeamAssignmentService;
use DateTimeImmutable;
use Doctrine\ORM\NonUniqueResultException;
use DomainException;
use Exception;

class Handler
{
    public function __construct(
        private readonly CallingRepository $calls,
        private readonly ClientRepository $clients,
        private readonly PartnerRepository $partners,
        private readonly DistanceDeterminant $determinant,
        private readonly TeamAssignmentService $teamAssignment,
        private readonly Flusher $flusher,
    ) {}

    /**
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function handle(Command $command): void
    {
        $lead = $command->getLead();
        $contact = $command->getContact();

        $call = $this->calls->findOneByNumber((string)$lead->getId());

        if (!$call) {
            throw new DomainException('Не найдена звонка ' . $lead->getId());
        }

        if ($lead->getStatus() === 38307946) {
            $call->setStatus(Status::waiting());
        } elseif ($lead->getStatus() === 38874646) {
            $call->setStatus(Status::assigned());
        }

        $client = $this->clients->findByPhone($contact->getPhone());
        $call->setClient($client);

        $partner = $this->partners->findOneByExternalId($lead->partnerExternalId);
        $call->setPartner($partner);

        $call->setUpdatedAt(new DateTimeImmutable());
        $call->setTitle($lead->getName());
        $call->setName($client->getName());
        $call->setPhone($client->getPhone());
        $call->setAddress($lead->address);
        $call->setAddressInfo($lead->addressInfo);
        $call->setDescription($lead->description ?: '');
        $call->setNosology($lead->nosology);
        $call->setChronicDiseases($lead->hz);
        $call->setLeadType($lead->leadType);
        $call->setSendPhone($lead->sendPhone);
        $call->setPartnerHospitalization($lead->partnerHospitalization);
        $call->setNoBusinessCards($lead->noBusinessCards);

        if ($lead->dateTime) {
            // $call->setDateTime(new DateTimeImmutable($lead->dateTime));
        }

        $this->determinant->toDetermine($call);

        $this->flusher->flush();

        $this->teamAssignment->toAppoint($call, $lead->team);
    }
}
