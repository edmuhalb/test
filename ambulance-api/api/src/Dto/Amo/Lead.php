<?php

declare(strict_types=1);

namespace App\Dto\Amo;

class Lead
{
    public int $id;
    public string $clientName;
    public string $clientPhone;
    public ?string $name = null;
    public ?int $statusId = null;
    public ?string $numberCalling = null;
    public ?string $dateTime = null;
    public ?string $address = null;
    public ?string $addressInfo = null;
    public ?string $team = null;
    public ?string $nosology = null;
    public ?string $age = null;
    public ?string $description = null;
    public ?string $hz = null;
    public ?string $leadType = null;
    public ?string $partnerExternalId = null;
    public ?string $partnerName = null;
    public bool $sendPhone = false;
    public bool $noBusinessCards = false;
    public bool $partnerHospitalization = false;

    public ?Employee $admin = null;
    public ?Employee $doctor = null;
    public ?int $operatorId = null;

    public function __construct(int $id, string $clientName, string $clientPhone)
    {
        $this->id = $id;
        $this->clientName = $clientName;
        $this->clientPhone = $clientPhone;
    }
}
