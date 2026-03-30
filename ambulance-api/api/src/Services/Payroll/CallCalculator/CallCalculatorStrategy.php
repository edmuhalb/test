<?php

declare(strict_types=1);

namespace App\Services\Payroll\CallCalculator;

use DomainException;

class CallCalculatorStrategy
{
    private array $strategies;

    public function __construct(
        PersonalCalculator $personalCalculator,
        RepeatCalculator $repeat,
        RepeatNextCalculator $repeatNextCalculator,
        RepeatTransmittedInitializerCalculator $transmittedInitializerCalculator,
        RepeatTransmittedExecutorCalculator $transmittedExecutorCalculator
    ) {
        $this->strategies = [
            'call_personal' => $personalCalculator,
            'call_repeat' => $repeat,
            'call_repeat_next' => $repeatNextCalculator,
            'call_repeat_transmitted_initializer' => $transmittedInitializerCalculator,
            'call_repeat_transmitted_executor' => $transmittedExecutorCalculator,
        ];
    }

    public function getStrategy($processor): CallCalculatorInterface
    {
        if (!isset($this->strategies[$processor])) {
            throw new DomainException(
                'Unknown payroll call strategy ' .
                $processor
            );
        }
        return $this->strategies[$processor];
    }
}
