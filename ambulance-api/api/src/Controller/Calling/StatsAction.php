<?php

declare(strict_types=1);

namespace App\Controller\Calling;

use App\Repository\CallingRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/calls/stats', name: 'call.stats', methods: ['GET'])]
class StatsAction extends AbstractController
{
    public function __construct(
        private readonly CallingRepository $callingRepository,
    ) {}
    public function __invoke(Request $request): JsonResponse
    {
        $queryBuilder = $this->callingRepository->createQueryBuilder('c');
        $this->applyFilters($queryBuilder, $request);
        $queryBuilder
            ->select('SUM(c.partnerReward) as totalPartnerReward', 'SUM(c.price) as price');

        $this->applyFilters($queryBuilder, $request);

        $result = $queryBuilder->getQuery()->getSingleResult();

        return $this->json([
            'partnerReward' => (int)$result['totalPartnerReward'] ?? 0,
            'amount' => (int)$result['price'] ?? 0,
        ]);
    }

    private function applyFilters(QueryBuilder $queryBuilder, Request $request): void
    {
        $partnerId = $request->query->get('partner_id');
        $completedAt = $request->query->all()['completedAt'] ?? [];
        $completedAtAfter = $completedAt['after'] ?? null;
        $completedAtBefore = $completedAt['before'] ?? null;
        $cityId = $request->query->get('city_id');
        $status = $request->query->get('status');

        if ($partnerId) {
            $queryBuilder
                ->andWhere('c.partner = :partnerId')
                ->setParameter('partnerId', $partnerId);
        }

        if ($completedAtAfter && $completedAtBefore) {
            $queryBuilder
                ->andWhere('c.completedAt >= :completedAtAfter')
                ->andWhere('c.completedAt <= :completedAtBefore')
                ->setParameter('completedAtAfter', $completedAtAfter)
                ->setParameter('completedAtBefore', $completedAtBefore);
        }

        if ($cityId) {
            $queryBuilder
                ->andWhere('c.city = :cityId')
                ->setParameter('cityId', $cityId);
        }

        if ($status) {
            $queryBuilder
                ->andWhere('c.status = :status')
                ->setParameter('status', $status);
        }
    }
}
