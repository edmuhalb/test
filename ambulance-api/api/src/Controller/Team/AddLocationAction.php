<?php

declare(strict_types=1);

namespace App\Controller\Team;

use App\Dto\Team\AddLocation;
use App\Entity\Team;
use App\Flusher;
use App\Repository\TeamLocationRepository;
use Http\Discovery\Exception\NotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class AddLocationAction extends AbstractController
{
    public function __invoke(AddLocation $dto, TeamLocationRepository $teams, Flusher $flusher): JsonResponse
    {
        if ($dto->id === 0) {
            throw new NotFoundException('Not found team 0');
        }
        $teamLocation = $teams->find($dto->id);
        if (!$teamLocation) {
            $teamLocation = new Team($dto->id);
            $teams->add($teamLocation);
        }

        $teamLocation->addLocation($dto->lat, $dto->lon);

        $flusher->flush();

        return $this->json(null, Response::HTTP_ACCEPTED);
    }
}
