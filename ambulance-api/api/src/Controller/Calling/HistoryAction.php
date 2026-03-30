<?php

declare(strict_types=1);

namespace App\Controller\Calling;

use App\Entity\Calling\Calling;
use App\Entity\Calling\Row;
use App\Repository\CallingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/calls/{id}/history', name: 'call.history', methods: ['GET'])]
class HistoryAction extends AbstractController
{
    public function __construct(
        private readonly CallingRepository $calls
    ) {}

    public function __invoke($id, Request $request): JsonResponse
    {
        $calls = $this->calls->getHistory((int)$id);
        return $this->json(array_map(static function (Calling $call) {
            return [
                'id' => $call->getId(),
                'status' => $call->getStatus(),
                'completedAt' => $call->getCompletedAt()?->format('Y-m-d H:i:s'),

                /** @var Row $service */
                'services' => array_map(static function ($service) {
                    return [
                        'name' => $service->getService()->getName(),
                        'price' => $service->getPrice(),
                    ];
                }, $call->getServices()->toArray()),
                'price' => $call->getPrice(),
                'client' => $call->getClient() ? [
                    'id' => $call->getClient()->getId(),
                    'name' => $call->getClient()->getName(),
                ]
                : null,
            ];
        }, $calls), Response::HTTP_ACCEPTED);
    }
}
