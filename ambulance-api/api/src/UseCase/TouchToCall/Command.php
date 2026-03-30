<?php

declare(strict_types=1);

namespace App\UseCase\TouchToCall;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    #[Assert\NotNull]
    public ?int $callId = null;
}
