<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\Calling\Calling;
use App\Repository\DeviceRepository;
use Exception;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;

readonly class CallingSender
{
    public function __construct(
        private DeviceRepository $devices,
        private Messaging $messaging,
    ) {}

    public function sendToAdmin(Calling $calling, string $title, string $body): void
    {
        $devices = $this->devices->findBy(['user' => $calling->getAdmin()]);

        foreach ($devices as $device) {
            try {
                $message = CloudMessage::fromArray([
                    'token' => $device->getId(),
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                    ],
                    'data' => [
                        'callingId' => $calling->getId(),
                        'callingStatus' => $calling->getStatus(),
                        'url' => 'calls',
                    ],
                ]);

                $this->messaging->send($message);
            } catch (Exception $e) {
            }
        }
    }
}
