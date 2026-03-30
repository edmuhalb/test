<?php

declare(strict_types=1);

namespace App\Services\Hospital;

use App\Entity\Hospital\Hospital;
use App\Query\PartnerReward\Fetcher;
use App\Query\PartnerReward\Query;
use Doctrine\DBAL\Exception;

class PartnerReward
{
    public function __construct(
        readonly private Fetcher $fetcher,
    ) {}

    /**
     * @throws Exception
     */
    public function calculate(Hospital $hospital): void
    {
        if ($hospital->getStatus() !== 'completed') {
            return;
        }

        $query = new Query(
            $hospital->getDischarged(),
            $hospital->getPartner()->getId(),
            2,
            0,
            0
        );

        $rewardPercent = $this->fetcher->fetch($query);
        $reward = $hospital->getMainAmount() / 100 * $rewardPercent;

        $hospital->setPartnerReward($reward);
    }
}
