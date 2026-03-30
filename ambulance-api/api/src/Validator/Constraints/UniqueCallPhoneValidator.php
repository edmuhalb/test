<?php

namespace App\Validator\Constraints;

use App\Repository\CallingRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use function Symfony\Component\String\u;

class UniqueCallPhoneValidator extends ConstraintValidator
{
    public function __construct(
        private readonly CallingRepository $calls
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof UniqueCallPhone) {
            throw new UnexpectedTypeException($constraint, UniqueCallPhone::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        $phone = preg_replace('/\D/', '', (string) $value);

        $call = $this->calls->findActiveCallByPhone($phone);

        if ($call) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ phone }}', (string) $value)
                ->setParameter(
                    '{{ address }}',
                    u($call->getAddress())->slice(0, -10)->toString() . ' ...'
                )
                ->setParameter('{{ time }}', $call->getCreatedAt()->format('d.m.Y H:i'))
                ->addViolation();
        }
    }
}