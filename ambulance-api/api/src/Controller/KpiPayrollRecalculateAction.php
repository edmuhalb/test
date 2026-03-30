<?php

declare(strict_types=1);

namespace App\Controller;

use App\Flusher;
use App\Repository\Payroll\KpiDocument\KpiDocumentRepository;
use App\Services\Payroll\KpiPayrollCalculator;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/v1/kpis/payroll-recalculate', name: 'api_v1_kpis_payroll_recalculate', methods: ['POST'])]
class KpiPayrollRecalculateAction extends AbstractController
{
    public function __construct(
        private readonly KpiDocumentRepository $documents,
        private readonly KpiPayrollCalculator $kpiCalculator,
        private readonly Flusher $flusher,
    ) {}

    public function __invoke(Request $request, KernelInterface $kernel): JsonResponse
    {
        ini_set('memory_limit', '-1');

        try {
            $documents = $this->documents->findAll();
            $count = 0;

            foreach ($documents as $document) {
                $this->kpiCalculator->calculate($document);

                $this->flusher->flush();
                $count++;
            }

            return $this->json([
                'countSuccess' => $count,
                'errors' => 0,
            ]);
        } catch (Exception $exception) {
            return $this->json([
                'countSuccess' => 0,
                'errors' => 1,
            ]);
        }
    }
}
