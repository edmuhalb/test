<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Entity\User\User;
use App\Repository\TeamRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Security\Core\User\UserInterface;

#[AsController]
class MyAction extends AbstractController
{
    public function __invoke(TeamRepository $teams): UserInterface
    {
        /** @var User $user */
        return $this->getUser();
    }
}
