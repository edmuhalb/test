<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\ApiUser;
use App\Repository\ApiUserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'user:init',
    description: 'Init user for exchange',
)]
class UserInitCommand extends Command
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly ApiUserRepository $users
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $user = new ApiUser();
        $user->setName('Exchange');
        $user->setPhone('79000000000');
        $user->setRoles(['ROLE_API_USER']);

        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            '111111'
        );

        $user->setPassword($hashedPassword);

        $this->users->save($user, true);

        $io->success('Новый пользователь добавлен.');

        return Command::SUCCESS;
    }
}
