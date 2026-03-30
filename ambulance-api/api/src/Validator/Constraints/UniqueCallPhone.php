<?php

namespace App\Validator\Constraints;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class UniqueCallPhone extends Constraint
{
    public string $message = 'Внимание, обращение от данного клиента с номером "{{ phone }}" уже в работе с {{ time }} по адресу "{{ address }}"';

    public function validatedBy(): string
    {
        return static::class.'Validator';
    }
}