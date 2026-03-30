<?php

declare(strict_types=1);

namespace App\Controller\Partner;

use App\Entity\Partner\PartnerUser;
use App\Services\AmbulanceApiClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class CallsGetAction extends AbstractController
{
    public function __construct(
        private readonly Security $security,
        private readonly AmbulanceApiClient $ambulanceApiClient,
    ) {}

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    #[Route('/api/partners/{id}/calls/{callId}', name: 'partner.calls.get.index', methods: ['GET'])]
    public function calls($id, $callId): JsonResponse
    {
        /** @var PartnerUser $user */
        $user = $this->security->getUser();
        $partner = $user->getPartner();

        if (!$partner) {
            return $this->json(['error' => 'Partner not found'], 400);
        }

        // Устанавливаем обязательные параметры с значениями по умолчанию
        $queryParams = [
            'partner' => $id,
            'id' => $callId,
        ];

        // Выполняем запрос к внешнему API через сервис
        $result = $this->ambulanceApiClient->requestAndGetResponse('call', $queryParams);

        return new JsonResponse($result['data'], $result['statusCode']);
    }
}
