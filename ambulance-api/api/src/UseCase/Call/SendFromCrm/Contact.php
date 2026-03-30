<?php

declare(strict_types=1);

namespace App\UseCase\Call\SendFromCrm;

use Symfony\Component\Validator\Constraints as Assert;

class Contact
{
    #[Assert\NotBlank]
    private ?string $name;

    #[Assert\NotBlank]
    private string $phone;

    public function __construct(?string $name, string $phone)
    {
        $this->name = $name;
        $this->phone = $phone;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }
}
