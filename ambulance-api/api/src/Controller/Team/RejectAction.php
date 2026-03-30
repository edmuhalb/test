<?php

declare(strict_types=1);

namespace App\Controller\Team;

use App\Entity\User\User;
use App\Flusher;
use App\Repository\TeamRepository;
use DateTimeImmutable;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class RejectAction extends AbstractController
{
    /**
     * @throws NonUniqueResultException
     */
    public function __invoke(TeamRepository $teams, Flusher $flusher): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $team = $teams->getActiveByAdministrator($user);

        $team->setReject(new DateTimeImmutable(), '');
        $flusher->flush();
        return $this->json(null, Response::HTTP_ACCEPTED);
    }
}
