<?php

declare(strict_types=1);

namespace App\Services\Payroll\ServiceCalculator;

use DomainException;

class ServiceCalculatorStrategy
{
    private array $processors;

    public function __construct(
        private TherapyPayrollCalculatorProcessor $therapyPayrollCalculatorProcessor,
        private CoddingPayrollCalculatorProcessor $coddingPayrollCalculatorProcessor,
        private TransportationPayrollCalculatorProcessor $transportationPayrollCalculatorProcessor,
        private SewingPayrollCalculatorProcessor $sewingPayrollCalculatorProcessor,
        private HospitalizationPayrollCalculatorProcessor $hospitalizationPayrollCalculatorProcessor,
    ) {
        $this->processors = [
            'service_therapy_calculator' => $this->therapyPayrollCalculatorProcessor,
            'service_codding_calculator' => $this->coddingPayrollCalculatorProcessor,
            'service_transportation_calculator' => $this->transportationPayrollCalculatorProcessor,
            'service_sewing_calculator' => $this->sewingPayrollCalculatorProcessor,
            'service_hospitalization_calculator' => $this->hospitalizationPayrollCalculatorProcessor,
        ];
    }

    public function getProcessor($processor): CallPayrollCalculatorProcessorInterface
    {
        if (!isset($this->processors[$processor])) {
            throw new DomainException(
                'Unknown payroll service processor ' .
                $processor
            );
        }

        return $this->processors[$processor];
    }
}
