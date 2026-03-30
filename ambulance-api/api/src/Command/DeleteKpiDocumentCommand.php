<?php

declare(strict_types=1);

namespace App\Command;

use App\Flusher;
use App\Repository\Payroll\KpiDocument\KpiDocumentRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'delete-kpi-document',
    description: 'Add a short description for your command',
)]
class DeleteKpiDocumentCommand extends Command
{
    public function __construct(
        private readonly KpiDocumentRepository $documents,
        private readonly Flusher $flusher,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        ini_set('memory_limit', '-1');

        $document = $this->documents->findById(2);

        if (!$document) {
            $output->writeln('<error>Document not found</error>');
            return Command::SUCCESS;
        }


        $this->documents->remove($document);

        $this->flusher->flush();

        return Command::SUCCESS;
    }
}
