<?php

namespace App\Entity;

use App\Repository\ApiCallLogRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ApiCallLogRepository::class)]
class ApiCallLog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $data = null;

    #[ORM\Column]
    private ?int $callId = null;

    #[ORM\Column]
    private ?int $responseStatus = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $responseData = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getData(): ?array
    {
        return $this->data;
    }

    public function setData(?array $data): static
    {
        $this->data = $data;

        return $this;
    }

    public function getCallId(): ?int
    {
        return $this->callId;
    }

    public function setCallId(int $callId): static
    {
        $this->callId = $callId;

        return $this;
    }

    public function getResponseStatus(): ?int
    {
        return $this->responseStatus;
    }

    public function setResponseStatus(?int $responseStatus): static
    {
        $this->responseStatus = $responseStatus;

        return $this;
    }

    public function getResponseData(): ?array
    {
        return $this->responseData;
    }

    public function setResponseData(?array $responseData): static
    {
        $this->responseData = $responseData;

        return $this;
    }
}
