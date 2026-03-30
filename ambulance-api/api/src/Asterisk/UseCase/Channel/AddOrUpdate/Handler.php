<?php

declare(strict_types=1);

namespace App\Asterisk\UseCase\Channel\AddOrUpdate;

use App\Asterisk\Repository\ChannelRepository;

readonly class Handler
{
    public function __construct(
        private ChannelRepository $channels,
    ) {}

    public function handle(Command $command): void
    {
        // TODO надо вынести валидации в модель Phone
        $clientPhone = preg_replace('/[^0-9]/', '', (string)$command->clientPhone);
        $teamPhone = preg_replace('/[^0-9]/', '', (string)$command->teamPhone);

        $hasChannel = $this->channels->hasChannelByClientPhoneNumber($clientPhone);
        if ($hasChannel) {
            $this->channels->update($clientPhone, $teamPhone);
        } else {
            $this->channels->create($clientPhone, $teamPhone);
        }
    }
}
