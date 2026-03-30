<?php

declare(strict_types=1);

namespace App\Command\Call;

use App\Flusher;
use App\Repository\CallingRepository;
use App\Services\Call\OperatorReward;
use App\Services\Call\PartnerReward;
use DateInterval;
use DatePeriod;
use DateTimeImmutable;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'calls:rewards:recalculate',
    description: 'Пересчитать награды операторов и партнеров за указанный период',
    aliases: ['calls:re:re']
)]
class RewardsRecalculateCommand extends Command
{
    public function __construct(
        private readonly CallingRepository $calls,
        private readonly PartnerReward $partnerRewards,
        private readonly OperatorReward $operatorReward,
        private readonly Flusher $flusher,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('start', InputArgument::REQUIRED, 'Начало периода в формате DD-MM-YYYY')
            ->addArgument('end', InputArgument::REQUIRED, 'Окончание периода в формате DD-MM-YYYY');
    }

    /**
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $start = $input->getArgument('start');
        $end = $input->getArgument('end');

        try {
            $period = new DatePeriod(
                (new DateTimeImmutable($start))->setTime(0, 0),
                new DateInterval('P1D'),
                (new DateTimeImmutable($end))->setTime(23, 59, 59)
            );
        } catch (Exception $e) {
            $io->error('Ошибка создания периода: ' . $e->getMessage());
            return Command::FAILURE;
        }

        foreach ($this->calls->findAllByCompletedAtFromPeriod($period) as $call) {
            $this->partnerRewards->calculate($call);
            $this->operatorReward->calculate($call);
            $this->flusher->flush();
        }

        $io->success('Пересчитаны награды партнеров и операторов. с: ' . $start . ' по: ' . $end);

        return Command::SUCCESS;
    }
}
