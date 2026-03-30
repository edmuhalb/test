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

#[Route('/api/v1/calls/payroll-recalculate/batch', name: 'api_v1_calls_payroll_recalculate_batch', methods: ['POST'])]
class CallPayrollRecalculateBatchAction extends AbstractController
{
    public function __construct(
        private readonly CallingRepository $calls,
        private readonly CallPayrollCalculator $employeePayrollCalculator,
        private readonly Flusher $flusher,
    ) {}

    public function __invoke(Request $request, KernelInterface $kernel): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $count = 0;
        $errors = [];

        foreach ($data['ids'] as $id) {
            try {
                $call = $this->calls->getById($id);

                $this->employeePayrollCalculator->calculate($call);
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
