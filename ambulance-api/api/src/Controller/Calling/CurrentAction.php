<?php

declare(strict_types=1);

namespace App\Controller\Calling;

use App\Entity\User\User;
use App\Repository\CallingRepository;
use App\Repository\TeamRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class CurrentAction extends AbstractController
{
    public function __invoke(TeamRepository $teams, CallingRepository $callings): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        if (!$user) {
            return $this->json(null, Response::HTTP_OK);
        }
        $call = $callings->findActiveByAdministrator($user);
        if (!$call) {
            return $this->json(null, Response::HTTP_OK);
        }

        return $this->json([
            'numberCalling' => $call->getNumberCalling(),
            'id' => $call->getId(),
            'title' => $call->getTitle(),
            'name' => $call->getName(),
            'phone' => $call->getPhone(),
            'address' => $call->getAddress(),
            'age' => $call->getAge(),
            'chronicDiseases' => $call->getChronicDiseases(),
            'nosology' => $call->getNosology(),

            'description' => $call->getDescription(),

            'dateTime' => $call->getDateTime(),
            'createdAt' => $call->getCreatedAt(),
            'acceptedAt' => $call->getCreatedAt(),
            'arrivedAt' => $call->getArrivedAt(),
            'completedAt' => $call->getCompletedAt(),

            'leadType' => $call->getLeadType(),
            'partnerName' => $call->getPartnerName(),
            'status' => $call->getStatus(),
            'admin' => [
                'id' => $call->getAdmin()?->getId(),
                'name' => $call->getAdmin()?->getName(),
            ],
            'doctor' => [
                'id' => $call->getDoctor()?->getId(),
                'name' => $call->getDoctor()?->getName(),
            ],
        ], Response::HTTP_OK);
    }
}
