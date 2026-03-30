<?php

declare(strict_types=1);

namespace App\Controller\PartnerApi\Report\Call;

use App\Security\PartnerUserIdentity;
use App\Query\Report\Partner\Call\CountForTheYear\Fetcher;
use App\Query\Report\Partner\Call\CountForTheYear\Query;
use DateTime;
use DateTimeImmutable;
use Doctrine\DBAL\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

#[Route('/partner/report/call/count-for-the-year', name: 'partner_report_call_count_for_the_year', methods: ['GET'])]
class CountForTheYear extends AbstractController
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
            (new DateTimeImmutable())->modify('-1 year')->format('Y-m-d'),
            (new DateTimeImmutable())->format('Y-m-d')
        ));

        $result = $this->createResult($data);

        return $this->json(
            array_column($result, 'amount'),
            Response::HTTP_ACCEPTED
        );
    }

    public function searchMonthAmount($month, $array)
    {
        foreach ($array as $element) {
            if ($element['month'] === $month['month'] && $element['year'] === $month['year']) {
                return $element['amount'];
            }
        }
        return 0;
    }

    private function createResult($array): array
    {
        $today = new DateTime();

        $date = clone $today;
        $months = [];
        for ($i = 1; $i <= 12; ++$i) {
            $months[] = [
                'amount' => 0,
                'month' => (int)$date->format('m'),
                'year' => (int)$date->format('Y'),
            ];
            $date->modify('first day of this month');
            $date->modify('-1 month');
        }
        $months = array_reverse($months);

        foreach ($months as $key => $month) {
            $months[$key]['amount'] = (int)$this->searchMonthAmount($month, $array);
        }

        return $months;
    }
}
