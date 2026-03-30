<?php

declare(strict_types=1);

namespace App\Dto\Amo\Lead;

class Field
{
    public string $id;
    public string $name;
    /**
     * @var Value[]
     */
    private array $values;

    /**
     * @return Value[]
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * @param Value[] $values
     */
    public function setValues(array $values): void
    {
        $this->values = $values;
    }
}
