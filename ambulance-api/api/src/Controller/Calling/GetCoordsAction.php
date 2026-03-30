<?php

declare(strict_types=1);

namespace App\Controller\Calling;

use App\Repository\CallingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class GetCoordsAction extends AbstractController
{
    public function __construct(
        private readonly CallingRepository $calls
    ) {}

    #[Route('/api/calls/coords', name: 'calls.get_coords', methods: ['GET'])]
    public function version(): JsonResponse
    {
        return $this->json(
            array_map(
                static fn ($call) => [$call['lat'], $call['lon']],
                $this->calls->getAllCoords()
            )
        );
    }
}
