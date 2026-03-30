<?php

declare(strict_types=1);

namespace App\Entity\Calling;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Embeddable]
class OperatorReward
{
    #[ORM\Column(options: ['default' => 0])]
    #[Groups(['calling:read'])]
    private int $therapy;

    #[ORM\Column(options: ['default' => 0])]
    #[Groups(['calling:read'])]
    private int $hospital;

    #[ORM\Column(options: ['default' => 0])]
    #[Groups(['calling:read'])]
    private int $coding;

    #[ORM\Column(options: ['default' => 0])]
    #[Groups(['calling:read'])]
    private int $stationary;

    #[ORM\Column(options: ['default' => 0])]
    #[Groups(['calling:read'])]
    private int $total;

    public function __construct(int $therapy, int $hospital, int $coding, int $stationary)
    {
        $this->therapy = $therapy;
        $this->hospital = $hospital;
        $this->coding = $coding;
        $this->stationary = $stationary;

        $this->total = $this->therapy + $this->hospital + $this->coding + $this->stationary;
    }

    public function getTherapy(): ?int
    {
        return $this->therapy;
    }

    public function getHospital(): int
    {
        return $this->hospital;
    }

    public function getCoding(): int
    {
        return $this->coding;
    }

    public function getStationary(): int
    {
        return $this->stationary;
    }

    public function getTotal(): int
    {
        return $this->total;
    }
}
