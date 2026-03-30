<?php

declare(strict_types=1);

namespace App\Services\MedTeam;

use App\Entity\MedTeam\MedTeam;
use App\Services\TelegramSender;

readonly class EmployeeNotification
{
    public function __construct(
        private TelegramSender $sender
    ) {}

    public function send(MedTeam $medTeam): void
    {
        $message = '🔥 ' . $medTeam->getPlannedStartAt()->format('d.m.Y') . "\r\n";
        $message .= $medTeam->getPlannedStartAt()->format('H:i') . ' - ' . $medTeam->getPlannedFinishAt()->format('H:i') . "\r\n";

        if ($medTeam->getPlannedDutyStartAt() && $medTeam->getPlannedDutyFinishAt()) {
            $message .=
                '️️️️️️️‼️️️️ Дежурство: ' .
                $medTeam->getPlannedDutyStartAt()->format('H:i') . ' - ' .
                $medTeam->getPlannedDutyFinishAt()->format('H:i') . "\r\n";
        }

        if ($medTeam->getDoctor()) {
            $message .= 'В: ' . $medTeam->getDoctor()->getName() . "\r\n";
        }

        if ($medTeam->getAdmin()) {
            $message .= 'А: ' . $medTeam->getAdmin()->getName() . "\r\n";
        }

        if ($medTeam->getDriver()) {
            $message .= 'Ш: ' . $medTeam->getDriver()->getName() . "\r\n";
        }

        if ($medTeam->getBase()) {
            $message .= 'База: ' . $medTeam->getBase()->getName() . "\r\n";
        }

        if ($medTeam->getCar()) {
            $message .= 'Авто: ' . $medTeam->getCar()->getName() . "\r\n";
        }

        if ($medTeam->getAdmin()) {
            $this->sender->send(
                $medTeam->getAdmin(),
                $message
            );
        }

        if ($medTeam->getDoctor()) {
            $this->sender->send(
                $medTeam->getDoctor(),
                $message
            );
        }

        if ($medTeam->getDriver()) {
            $this->sender->send(
                $medTeam->getDriver(),
                $message
            );
        }
    }
}
