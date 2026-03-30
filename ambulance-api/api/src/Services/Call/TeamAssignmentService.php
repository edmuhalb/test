<?php

declare(strict_types=1);

namespace App\Services\Call;

use App\Entity\Calling\Calling;
use App\Entity\Calling\Status;
use App\Flusher;
use App\Repository\MedTeam\MedTeamRepository;
use App\Services\CallingSender;
use App\Services\TelegramSender;
use DomainException;

readonly class TeamAssignmentService
{
    public function __construct(
        private MedTeamRepository $teams,
        private Flusher $flusher,
        private CallingSender $sender,
        private TelegramSender $tgSender,
    ) {}

    public function toAppoint(Calling $call, ?int $teamId): void
    {
        if (!$teamId) {
            return;
        }

        if ($call->getStatus() !== Status::ASSIGNED) {
            return;
        }

        $medTeam = $this->teams->getLastWorkByNumber($teamId);

        if (!$medTeam) {
            throw new DomainException('Не найдено рабочей бригады № ' . $teamId);
        }

        if ($call->getTeam() === $medTeam) {
            return;
        }

        $call->setTeam($medTeam);
        $call->setAdmin($medTeam->getAdmin());
        $call->setDoctor($medTeam->getDoctor());

        $this->flusher->flush();

        if ($call->getAdmin()) {
            $this->tgSender->send($call->getAdmin(), "‼️️️️ ВНИМАНИЕ ‼️\nУ вас новый вызов, зайдите в приложение");
        }

        if ($call->getDoctor()) {
            $this->tgSender->send($call->getDoctor(), "‼️️️️ ВНИМАНИЕ ‼️\nУ вас новый вызов, зайдите в приложение");
        }

        if ($call->getTeam()?->getDriver()) {
            $this->tgSender->send($call->getTeam()->getDriver(), "‼️️️️ ВНИМАНИЕ ‼️\nУ вас новый вызов, зайдите в приложение");
        }

        $this->sender->sendToAdmin(
            $call,
            'Внимание новый заказ',
            $call->getAddress()
        );
    }
}
