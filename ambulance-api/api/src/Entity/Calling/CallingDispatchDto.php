<?php

declare(strict_types=1);

namespace App\Entity\Calling;

use Symfony\Component\Serializer\Annotation\Groups;

class CallingDispatchDto
{
    #[Groups(['calling:write'])]
    public ?string $arrivalDateTime = null;
}
