<?php

declare(strict_types=1);

namespace App\Entity\PaymentSetting;

use App\Repository\PaymentSetting\PaymentSettingVersionRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: PaymentSettingVersionRepository::class)]
#[ORM\Table(name: 'payment_setting_version')]
class PaymentSettingVersion
{
    use BlameableEntity;
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'versions')]
    #[ORM\JoinColumn(nullable: false)]
    private PaymentSetting $paymentSetting;

    #[ORM\Column]
    private int $value;

    public function __construct(PaymentSetting $paymentSetting, int $value)
    {
        $this->paymentSetting = $paymentSetting;
        $this->value = $value;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPaymentSetting(): PaymentSetting
    {
        return $this->paymentSetting;
    }

    public function getValue(): int
    {
        return $this->value;
    }
}
