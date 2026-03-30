<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'pbx:test-call',
    description: 'Add a short description for your command',
)]
class PbxTestCallCommand extends Command
{
    public function __construct(
        private HttpClientInterface $client,
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('clientPhone', InputArgument::REQUIRED, 'Client phone')
            ->addArgument('adminPhone', InputArgument::REQUIRED, 'Admin phone');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $clientPhone = $input->getArgument('clientPhone');
        $adminPhone = $input->getArgument('adminPhone');
        $clientPhone = preg_replace('/[^0-9]/', '', $clientPhone);

        $adminPhone = preg_replace('/[^0-9]/', '', $adminPhone);

        $response =$this->client->request('POST', 'https://pbx.reset-med.ru:8089/ari/channels', [
            'auth_basic' => 'ARI_user:G5heZ8ld03V1I3',
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'body' => [
                'endpoint' => 'Local/' . $adminPhone . '@from-inclient',
                'extension' => $clientPhone,
                'context' => 'from-inclient',
                'priority' => '1',
            ],
        ]);

        dump($response->toArray(false));

        $io->success('Call sent.');

        return Command::SUCCESS;
    }
}
