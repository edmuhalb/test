<?php

declare(strict_types=1);

namespace App\Controller;

use App\Flusher;
use App\Repository\MedTeam\MedTeamRepository;
use App\Services\Payroll\ShiftPayrollCalculator;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/v1/shifts/payroll-recalculate/batch', name: 'api_v1_shifts_payroll_recalculate_batch', methods: ['POST'])]
class ShiftPayrollRecalculateBatchAction extends AbstractController
{
    public function __construct(
        private readonly MedTeamRepository $shifts,
        private readonly ShiftPayrollCalculator $shiftPayrollCalculator,
        private readonly Flusher $flusher,
    ) {}

    public function __invoke(Request $request, KernelInterface $kernel): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $count = 0;
        $errors = [];

        foreach ($data['ids'] as $id) {
            try {
                $shift = $this->shifts->getById($id);

                $this->shiftPayrollCalculator->calculate($shift);
                $this->flusher->flush();
                ++$count;
            } catch (Exception $exception) {
                $errors[$id] = $exception->getMessage();
            }
        }

        return $this->json([
            'countSuccess' => $count,
            'errors' => $errors,
        ]);
    }
}
