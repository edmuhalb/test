<?php

declare(strict_types=1);

namespace App\Controller\Partner;

use App\Services\AmbulanceApiClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class CallsAction extends AbstractController
{
    public function __construct(
        private readonly AmbulanceApiClient $ambulanceApiClient,
    ) {}

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    #[Route('/api/partners/{id}/calls', name: 'partner.calls.index', methods: ['GET'])]
    public function calls($id, Request $request): JsonResponse
    {
        // Устанавливаем обязательные параметры с значениями по умолчанию
        $queryParams = [
            'partner' => $id,
            'page' => $request->query->getInt('page', 1),
            'perPage' => $request->query->getInt('per_page', 20),
            'amounts' => 'true'
        ];
        
        // Добавляем остальные параметры из запроса, исключая пустые значения и уже обработанные
        $allParams = $request->query->all();
        $excludedKeys = ['per_page', 'page', 'partner']; // Уже обработаны выше
        
        foreach ($allParams as $key => $value) {
            // Пропускаем уже обработанные параметры и пустые значения
            if (!in_array($key, $excludedKeys, true) && $value !== '' && $value !== null) {
                $queryParams[$key] = $value;
            }
        }

        // Выполняем запрос к внешнему API через сервис
        $result = $this->ambulanceApiClient->requestAndGetResponse('calls', $queryParams);

        return new JsonResponse($result['data'], $result['statusCode']);
    }
}
