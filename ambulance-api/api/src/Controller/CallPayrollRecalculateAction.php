<?php

declare(strict_types=1);

namespace App\Controller;

use App\Flusher;
use App\Repository\CallingRepository;
use App\Services\Payroll\CallPayrollCalculator;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/v1/calls/{id}/payroll-recalculate', name: 'api_v1_calls_payroll_recalculate', methods: ['POST'])]
class CallPayrollRecalculateAction extends AbstractController
{
    public function __construct(
        private readonly CallingRepository $calls,
        private readonly CallPayrollCalculator $employeePayrollCalculator,
        private readonly Flusher $flusher,
    ) {}

    public function __invoke(int $id, Request $request, KernelInterface $kernel): JsonResponse
    {
        try {
            $call = $this->calls->getById($id);

            $this->employeePayrollCalculator->calculate($call);
            $this->flusher->flush();
            return $this->json(['status' => 'success']);
        } catch (Exception $exception) {
            return $this->json([
                'status' => 'failure',
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
