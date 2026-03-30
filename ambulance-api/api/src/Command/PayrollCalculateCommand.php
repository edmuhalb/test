<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\MedTeam\MedTeam;
use App\Flusher;
use App\Repository\MedTeam\MedTeamRepository;
use App\Services\Payroll\ShiftPayrollCalculator;
use DateTimeImmutable;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'payroll:calculate',
    description: 'Add a short description for your command',
)]
class PayrollCalculateCommand extends Command
{
    public function __construct(
        private readonly MedTeamRepository $shifts,
        private readonly ShiftPayrollCalculator $shiftPayrollCalculator,
        private readonly Flusher $flusher
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        ini_set('memory_limit', '-1');

        $shifts = $this->shifts->findByPlanned(
            new DateTimeImmutable('2025-04-01T00:00:00.000Z'),
            new DateTimeImmutable('2025-05-01T00:00:00.000Z'),
        );

        /** @var MedTeam $shift */
        foreach ($shifts as $shift) {
            if ($shift->getStatus() !== 'completed') {
                continue;
            }
            $this->shiftPayrollCalculator->calculate($shift);
            $this->flusher->flush();
        }

        return Command::SUCCESS;
    }
}
