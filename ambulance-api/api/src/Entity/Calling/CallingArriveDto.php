<?php

declare(strict_types=1);

namespace App\Entity\Calling;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class CallingArriveDto
{
    #[Assert\NotNull]
    #[Groups(['calling:write'])]
    public ?string $fio = null;
    #[Groups(['calling:write'])]
    public ?string $passport = null;
    #[Assert\NotNull]
    #[Groups(['calling:write'])]
    public ?string $age = null;

    #[Groups(['calling:write'])]
    public ?string $note = null;
    #[Groups(['calling:write'])]
    public ?string $endOfServiceDateTime = null;
    #[Groups(['calling:write'])]
    public ?string $dateOfBirth = null;
}
