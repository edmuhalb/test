<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\ApiUser;
use App\Repository\ApiUserRepository;
use App\Repository\CallingRepository;
use App\Services\BuhClient;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'test:call',
    description: 'Init user for exchange',
)]
class TestCommand extends Command
{
    public function __construct(
        private readonly BuhClient         $client,
        private readonly CallingRepository $calls,
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $call = $this->calls->getById(51730);
        $this->client->send($call);

        return Command::SUCCESS;
    }
}
