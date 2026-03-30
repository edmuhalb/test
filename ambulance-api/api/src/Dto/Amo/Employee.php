<?php

declare(strict_types=1);

namespace App\Dto\Amo;

class Employee
{
    private int $id;
    private string $name;
    private string $role;

    public function __construct(int $id, string $name, string $role)
    {
        $this->id = $id;
        $this->name = $name;
        $this->role = $role;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getRole(): string
    {
        return $this->role;
    }
}
