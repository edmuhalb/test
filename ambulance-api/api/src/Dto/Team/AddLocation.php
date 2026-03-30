<?php

declare(strict_types=1);

namespace App\Dto\Team;

use Symfony\Component\Serializer\Annotation\Groups;

class AddLocation
{
    #[Groups(['team:write'])]
    public ?int $id = null;

    #[Groups(['team:write'])]
    public ?string $lon = null;

    #[Groups(['team:write'])]
    public ?string $lat = null;
}
