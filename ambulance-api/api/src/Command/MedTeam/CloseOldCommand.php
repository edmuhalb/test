<?php

declare(strict_types=1);

namespace App\Command\MedTeam;

use App\Entity\MedTeam\MedTeam;
use App\Flusher;
use App\Repository\MedTeam\MedTeamRepository;
use DateTimeImmutable;
use DateTimeZone;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'med-team:close-old',
    description: 'Закрыть все открытые смены которые работают дольше чем 3 часа от даты планируемого завершения',
)]
class CloseOldCommand extends Command
{
    public function __construct(
        private readonly MedTeamRepository $teams,
        private readonly Flusher $flusher
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $teams = $this->teams->findAllWorking();

        $count = 0;

        /** @var MedTeam $team */
        foreach ($teams as $team) {
            $mskTimeZone = new DateTimeZone('Europe/Moscow');

            if ($team->getPlannedFinishAt() < (new DateTimeImmutable('now', $mskTimeZone))->modify('-3 hours')) {
                $team->setCompletedAt($team->getPlannedFinishAt());
                $team->setStatus('completed');
                ++$count;
            }
        }

        $this->flusher->flush();

        if ($count > 0) {
            $io->warning('Закрыто ' . $count . ' смен');
        } else {
            $io->success('Нет открытых смен');
        }

        return Command::SUCCESS;
    }
}
