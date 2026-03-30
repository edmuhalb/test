<?php

declare(strict_types=1);

namespace App\Controller\Calling;

use App\Entity\Calling\Calling;
use App\Entity\Calling\Status;
use App\Flusher;
use App\Services\BuhClient;
use App\Services\WSClient;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class NotReadyAction extends AbstractController
{
    public function __construct(
        private readonly WSClient $wsClient,
        private readonly BuhClient $buhClient,
    ) {}

    public function __invoke(Calling $call, Flusher $flusher): JsonResponse
    {
        $call->setStatus(Status::notReady());

        $flusher->flush();

        $this->wsClient->sendUpdateOffer($call->getId());

        try {
            $this->buhClient->send($call);
        }catch (Exception $e) {

        }

        return $this->json($call, Response::HTTP_ACCEPTED);
    }
}
