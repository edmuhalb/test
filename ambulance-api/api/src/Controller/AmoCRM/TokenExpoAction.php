<?php

declare(strict_types=1);

namespace App\Controller\AmoCRM;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/expo/{token}', name: 'api-expo-token-log', methods: ['GET'])]
class TokenExpoAction extends AbstractController
{
    public function __invoke(string $token, Request $request): JsonResponse
    {
        file_put_contents(
            \dirname(__DIR__) . '/../../var/expo-token-' . date('Y-m-d H:i:s') . '.txt',
            print_r($token . PHP_EOL, true),
            FILE_APPEND
        );

        return $this->json([], Response::HTTP_OK);
    }
}
