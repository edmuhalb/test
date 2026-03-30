<?php

declare(strict_types=1);

namespace App\UseCase\Client\Create;

use App\Entity\Client;
use App\Flusher;
use App\Repository\ClientRepository;
use Doctrine\ORM\NonUniqueResultException;
use DomainException;

class Handler
{
    public function __construct(
        private readonly ClientRepository $clients,
        private readonly Flusher $flusher
    ) {}

    /**
     * @throws NonUniqueResultException
     */
    public function handle(Command $command): void
    {
        $client = $this->clients->findByPhone($command->getPhone());
        if ($client) {
            throw new DomainException('клиент с телефоном ' . $command->getPhone() . ' уже существует');
        }

        $client = new Client(
            $command->getPhone(),
            $command->getName()
        );

        $this->clients->add($client);
        $this->flusher->flush();
    }
}
