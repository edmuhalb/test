<?php

declare(strict_types=1);

namespace App\Entity\Money;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class Money
{
    #[ORM\Column(type: 'money_currency', length: 3, nullable: false)]
    public Currency $currency;

    public function __construct(
        #[ORM\Column(nullable: false)]
        public int $amount,
    ) {
        $this->currency = new Currency('RUB');
    }
}
