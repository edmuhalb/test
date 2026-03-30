<?php

declare(strict_types=1);

namespace App\Telegram;

use App\Services\YaDiskApi\YaDiskApiInterface;
use BoShurik\TelegramBotBundle\Telegram\Command\CommandInterface;
use DateTime;
use DomainException;
use TelegramBot\Api\BotApi;
use TelegramBot\Api\Types\ReplyKeyboardRemove;
use TelegramBot\Api\Types\Update;

readonly class GetAudioCommand implements CommandInterface
{
    public function __construct(
        private YaDiskApiInterface $api
    ) {}

    public function execute(BotApi $api, Update $update): void
    {
        $fileId = $update->getMessage()->getAudio()->getFileId();
        $fileSize = $update->getMessage()->getAudio()->getFileSize();

        if ($fileSize > 20000000) {
            $api->deleteMessage(
                $update->getMessage()->getChat()->getId(),
                $update->getMessage()->getMessageId()
            );

            $api->sendMessage(
                $update->getMessage()->getChat()->getId(),
                'Не могу принять файл размером более 20МБ',
                'markdown',
                false,
                null,
                new ReplyKeyboardRemove()
            );

            return;
        }

        $tgFile = $api->getFile($fileId);

        $filePath = $tgFile->getFilePath();

        $extension = pathinfo($filePath, PATHINFO_EXTENSION);

        $file = $api->downloadFile($fileId);

        $timestamp = (new DateTime())->format('YmdHis');
        // $filePath = dirname(__DIR__) . "/../var/{$timestamp}.{$extension}";

        $tempFile = tempnam(sys_get_temp_dir(), 'audio');

        if (!$tempFile) {
            throw new DomainException('Unable to create temporary file');
        }

        file_put_contents(
            $tempFile,
            $file
        );

        $this->api->upload($tempFile, "disk:/Аудиозаписи вызовов/11/{$timestamp}.{$extension}");

        $api->deleteMessage(
            $update->getMessage()->getChat()->getId(),
            $update->getMessage()->getMessageId()
        );

        $api->sendMessage(
            $update->getMessage()->getChat()->getId(),
            'Ваша аудиозапись получена',
            'markdown',
            false,
            null,
            new ReplyKeyboardRemove()
        );
    }

    public function isApplicable(Update $update): bool
    {
        if (!$update->getMessage()?->getAudio()) {
            return false;
        }

        return true;
    }
}
