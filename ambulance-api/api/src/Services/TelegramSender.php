<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\User\User;
use App\Repository\TgChatRepository;
use App\Repository\UserRepository;
use TelegramBot\Api\BotApi;
use TelegramBot\Api\Types\ReplyKeyboardRemove;

readonly class TelegramSender
{
    public function __construct(
        private BotApi $botApi,
        private TgChatRepository $tgChatRepository,
        private UserRepository $users,
    ) {}

    public function send(?User $user, string $message): void
    {
        if (!$user) {
            return;
        }

        $chats = $this->tgChatRepository->findByUser($user);
        foreach ($chats as $chat) {
            $this->botApi->sendMessage(
                (int)$chat->getChatId(),
                $message,
                'markdown',
                false,
                null,
                new ReplyKeyboardRemove()
            );
        }
    }

    public function sendByRoleId(int $roleId, string $message): void
    {
        $users = $this->users->findAllActiveByRoleId($roleId);

        if (empty($users)) {
            return;
        }

        foreach ($users as $user) {
            $this->send($user, $message);
        }
    }
}
