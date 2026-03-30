<?php

declare(strict_types=1);

namespace App\Controller\Calling;

use App\Entity\Calling\Calling;
use App\Flusher;
use App\Repository\CallingRepository;
use DateTimeImmutable;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class CoddingAction extends AbstractController
{
    /**
     * @throws NonUniqueResultException
     */
    public function __invoke(Calling $calling, CallingRepository $callings, Flusher $flusher): JsonResponse
    {
        // $calling->setComplete(new DateTimeImmutable());
        $flusher->flush();
        return $this->json($calling, Response::HTTP_ACCEPTED);
    }
}
