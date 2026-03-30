<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/api/version', name: 'version')]
    public function version(): Response
    {
        return $this->json([
            'min' => $this->getParameter('app.min_version'),
            'target' => $this->getParameter('app.target_version'),
        ]);
    }

    #[Route('/api/v1/version-info', name: 'version-info')]
    public function versionInfo(): Response
    {
        return $this->json([
            'min' => $this->getParameter('app.min_version'),
            'target' => $this->getParameter('app.target_version'),
        ]);
    }

    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->json(null);
    }
}
