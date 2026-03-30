<?php

declare(strict_types=1);

namespace App\Controller\AmoCRM;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Exceptions\AmoCRMoAuthApiException;
use App\Repository\AmoCrmTokenRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/amo-crm/auth/callback', name: 'amo-crm_auth-callback', methods: ['GET'])]
class AuthCallbackAction extends AbstractController
{
    public function __construct(
        private readonly AmoCrmTokenRepository $tokens
    ) {}

    /**
     * @throws AmoCRMoAuthApiException
     */
    public function __invoke(Request $request): JsonResponse
    {
        $client = new AmoCRMApiClient(
            'd80b0f1f-1687-4b1e-8abd-9f3cbbe7a19e',
            'fCUzh7hiQ1bcuKQSrdJVp7Mnwnwi4b2vsK4W7yzhBCcumEkvRcHl3wX3hVxglhmK',
            'https://reset-med.ru/app/api/amo-crm/auth/callback'
        );

        if (isset($_GET['referer'])) {
            $client->setAccountBaseDomain($_GET['referer']);
        }

        $accessToken = $client->getOAuthClient()->getAccessTokenByCode($request->get('code'));

        $this->tokens->update($accessToken, $client->getAccountBaseDomain());

        return $this->json([], Response::HTTP_OK);
    }
}
