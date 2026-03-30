<?php

declare(strict_types=1);

namespace App\Services\Call\CrmCallFetcher;

class Lead
{
    private const LEAD_STATUSES = [
        38307946, // Выбери бригаду
        38874646, // Бригада назначена
        62358394, // Приняли
        38187418,  // Выехали
    ];

    public ?string $numberCalling = null;
    public ?int $mainContactId = null;

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
    private ?string $name;
    private int $status;
    private int $pipelineId;
    private ?int $operatorId = null;

    public function __construct(int $id, int $status, int $pipelineId, ?string $name = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->status = $status;
        $this->pipelineId = $pipelineId;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getPipelineId(): int
    {
        return $this->pipelineId;
    }

    public function isPipelineHouseCall(): bool
    {
        return $this->pipelineId === 4018768;
    }

    public function isSuitableStatus(): bool
    {
        return \in_array($this->status, self::LEAD_STATUSES, true);
    }

    public function getOperatorId(): ?int
    {
        return $this->operatorId;
    }

    public function setOperatorId(?int $operatorId): void
    {
        $this->operatorId = $operatorId;
    }
}
