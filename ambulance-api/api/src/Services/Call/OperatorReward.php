<?php

declare(strict_types=1);

namespace App\Services\Call;

use App\Entity\Calling\Calling;
use App\Entity\Calling\Status;
use App\Repository\PaymentSetting\PaymentSettingRepository;

class OperatorReward
{
    public function __construct(
        private readonly PaymentSettingRepository $paymentSettings
    ) {}

    public function calculate(Calling $call): void
    {
        if ($call->getStatus() !== Status::COMPLETED) {
            return;
        }

        $therapy = 0;
        $hospital = 0;
        $coding = 0;
        $stationary = 0;

        foreach ($call->getServices() as $row) {
            if ($row->isTherapy()) {
                $therapy += $row->getPrice();
            } elseif ($row->isHospital()) {
                $hospital += $row->getPrice();
            } elseif ($row->isCoding()) {
                $coding += $row->getPrice();
            } elseif ($row->isStationary()) {
                ++$stationary;
            }
        }

        $therapy = $therapy / 100 * $this->paymentSettings->getOperatorPercentTherapy();
        $hospital = $hospital / 100 * $this->paymentSettings->getOperatorPercentHospital();
        $coding = $coding / 100 * $this->paymentSettings->getOperatorPercentCoding();
        $stationary *= $this->paymentSettings->getOperatorRewardStationary();

        $call->setOperatorReward(
            new \App\Entity\Calling\OperatorReward(
                (int)$therapy,
                (int)$hospital,
                (int)$coding,
                $stationary
            )
        );
    }
}
