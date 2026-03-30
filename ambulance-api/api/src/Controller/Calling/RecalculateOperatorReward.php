<?php

declare(strict_types=1);

namespace App\Controller\Calling;

use App\Flusher;
use App\Repository\CallingRepository;
use App\Services\Call\OperatorReward;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class RecalculateOperatorReward extends AbstractController
{
    public function __invoke(CallingRepository $callings, Flusher $flusher, OperatorReward $operatorReward): JsonResponse
    {
        foreach ($callings->findAllWhoHasOperator() as $calling) {
            $operatorReward->calculate($calling);
        }

        $flusher->flush();

        return $this->json([], Response::HTTP_OK);
    }
}
