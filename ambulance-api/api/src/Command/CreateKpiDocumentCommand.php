<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Payroll\KpiDocument\KpiDocument;
use App\Flusher;
use App\Repository\Payroll\KpiDocument\KpiDocumentRepository;
use App\Services\Payroll\KpiPayrollCalculator;
use DateTimeImmutable;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'create-kpi-document',
    description: 'Add a short description for your command',
)]
class CreateKpiDocumentCommand extends Command
{
    public function __construct(
        private readonly KpiDocumentRepository $documents,
        private readonly KpiPayrollCalculator $kpiCalculator,
        private readonly Flusher $flusher,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        ini_set('memory_limit', '-1');
       // $document = $this->documents->findById(1);
//
       // if (!$document) {
       //     $document = new KpiDocument(
       //         new DateTimeImmutable('2024-12-01T00:00:00.000Z'),
       //         new DateTimeImmutable('2025-01-01T00:00:00.000Z'),
       //     );
//
       //     $this->documents->add($document);
       // }
//
       // $this->kpiCalculator->calculate($document);
//
       // $this->flusher->flush();
//
       // $document = $this->documents->findById(2);


            $document = new KpiDocument(
                new DateTimeImmutable('2025-02-01T00:00:00.000Z'),
                new DateTimeImmutable('2025-03-01T00:00:00.000Z'),
            );

            $this->documents->add($document);


        $this->kpiCalculator->calculate($document);

        $this->flusher->flush();

        return Command::SUCCESS;
    }
}
