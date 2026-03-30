<?php

declare(strict_types=1);

namespace App\UseCase\WorkSchedule\Batch;

use DateTimeImmutable;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    #[Assert\NotBlank]
    public ?int $user = null;

    #[Assert\NotBlank]
    public ?DateTimeImmutable $dateStart = null;

    #[Assert\NotBlank]
    public ?DateTimeImmutable $dateEnd = null;

    #[Assert\NotBlank]
    #[Assert\Choice(choices: [
        'daytime', // дневная *
        'night',   // ночная
        'evening', // вечерняя
        'day',     // суточная
        'stop',     // выходной
        'clear',     // очистить
    ])]
    public ?string $type = null;

    #[Assert\NotBlank]
    public ?string $role = null;

    #[Assert\NotBlank]
    public ?int $city = null;
}
