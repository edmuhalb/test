<?php

declare(strict_types=1);

namespace App\Telegram;

use App\Entity\TgChat;
use App\Flusher;
use App\Repository\TgChatRepository;
use App\Repository\UserRepository;
use BoShurik\TelegramBotBundle\Telegram\Command\CommandInterface;
use TelegramBot\Api\BotApi;
use TelegramBot\Api\Types\ReplyKeyboardRemove;
use TelegramBot\Api\Types\Update;

readonly class GetContactCommand implements CommandInterface
{
    public function __construct(
        private UserRepository $users,
        private TgChatRepository $tgChatRepository,
        private Flusher $flusher,
    ) {}

    public function execute(BotApi $api, Update $update): void
    {
        $phone = $update->getMessage()->getContact()->getPhoneNumber();
        $chatId = $update->getMessage()->getChat()->getId();
        $user = $this->users->findOneByPhone($phone);

        if (!$user) {
            $api->sendMessage(
                $update->getMessage()->getChat()->getId(),
                "Пользователь с таким контактом не найден, обратитесь к администратору\n‼️Внимание Запускайте бота со служебного номера телефона",
                'markdown',
                false,
                null,
                new ReplyKeyboardRemove()
            );
        } else {
            $chat = $this->tgChatRepository->findOneByChatId((string)$chatId);

            if (!$chat) {
                $chat = new TgChat();
                $chat->setChatId((string)$chatId);
                $this->tgChatRepository->add($chat);
            }

            $chat->setUser($user);

            $this->flusher->flush();

            $message = 'Привет, ' . $chat->getUser()->getName() . ". Все получилось!\nВ этот чат тебе будут приходить уведомления о предстоящей смене";

            $api->sendMessage(
                $update->getMessage()->getChat()->getId(),
                $message,
                'markdown',
                false,
                null,
                new ReplyKeyboardRemove()
            );
        }
    }

    public function isApplicable(Update $update): bool
    {
        if (!$update->getMessage()?->getContact()) {
            return false;
        }

        return true;
    }
}
