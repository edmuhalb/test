<?php

declare(strict_types=1);

namespace App\Controller\PartnerApi;

use App\Controller\PaginationSerializer;
use App\Entity\Hospital\Hospital;
use App\Security\PartnerUserIdentity;
use App\Repository\Hospital\HospitalRepository;
use App\Services\AmbulanceApiClient;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class PartnerHospitalsAction extends AbstractController
{
    private const PER_PAGE = 50;

    public function __construct(
        private readonly Security $security,
        private readonly AmbulanceApiClient $ambulanceApiClient,
    ) {}

    #[Route('/partner/hospitals', name: 'partner-api.hospitals.index', methods: ['GET'])]
    public function hospitals(Request $request): JsonResponse
    {
        /** @var PartnerUserIdentity $user */
        $user = $this->security->getUser();
        if (!$user instanceof PartnerUserIdentity) {
            return $this->json(['error' => 'Partner not found'], 400);
        }

        // Устанавливаем обязательные параметры с значениями по умолчанию
        $queryParams = [
            'partner' => $user->getPartnerId(),
            'page' => $request->query->getInt('page', 1),
            'perPage' => $request->query->getInt('per_page', 20),
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
        $result = $this->ambulanceApiClient->requestAndGetResponse('placements', $queryParams);

        return new JsonResponse($result['data'], $result['statusCode']);
    }
}
