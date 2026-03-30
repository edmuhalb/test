<?php

declare(strict_types=1);

namespace App\Controller\PartnerApi\Report\Call;

use App\Security\PartnerUserIdentity;
use App\Query\Report\Partner\Call\NumberByStatus\Fetcher;
use App\Query\Report\Partner\Call\NumberByStatus\Query;
use Doctrine\DBAL\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

#[Route('/partner/report/call/number-by-status', name: 'partner_report_call_number_by_status', methods: ['GET'])]
class NumberByStatus extends AbstractController
{
    public function __construct(
        private readonly Security $security,
    ) {}

    /**
     * @throws Exception
     */
    public function __invoke(Fetcher $fetcher): JsonResponse
    {
        /** @var PartnerUserIdentity $user */
        $user = $this->security->getUser();
        if (!$user instanceof PartnerUserIdentity) {
            throw $this->createAccessDeniedException('Partner not found');
        }

        $data = $fetcher->fetch(new Query(
            $user->getPartnerId(),
        ));

        $result = [
            'not_ready' => 0,
            'waiting' => 0,
            'assigned' => 0,
            'accepted' => 0,
            'dispatched' => 0,
            'arrived' => 0,
            'completed' => 0,
            'rejected' => 0,
        ];

        $total = 0;
        foreach ($data as $item) {
            $result[$item['status']] = $item['count'];
            $total += $item['count'];
        }

        $result['total'] = $total;

        return $this->json($result);
    }
}
