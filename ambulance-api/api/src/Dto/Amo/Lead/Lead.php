<?php

declare(strict_types=1);

namespace App\Dto\Amo\Lead;

use DateTimeImmutable;
use Symfony\Component\Serializer\Annotation\SerializedName;

class Lead
{
    public string $id;
    public string $name;
    /**
     * @SerializedName("pipeline_id")
     */
    public string $pipelineId;
    /**
     * @SerializedName("status_id;")
     */
    public string $statusId;
    /**
     * @SerializedName("old_status_id")
     */
    public ?string $oldStatusId = null;
    /**
     * @SerializedName("created_at")
     */
    private DateTimeImmutable $createdAt;
    /**
     * @SerializedName("updated_at")
     */
    private DateTimeImmutable $updatedAt;

    /**
     * @var Field[]
     * @SerializedName("custom_fields")
     */
    private array $fields;

    /**
     * @return Field[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @param Field[] $fields
     */
    public function setFields(array $fields): void
    {
        $this->fields = $fields;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(string $createdAt): void
    {
        $datetime = new DateTimeImmutable();

        $datetime = $datetime->setTimestamp((int)$createdAt);

        $this->createdAt = $datetime;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(string $updatedAt): void
    {
        $datetime = new DateTimeImmutable();

        $datetime = $datetime->setTimestamp((int)$updatedAt);

        $this->updatedAt = $datetime;
    }
}
