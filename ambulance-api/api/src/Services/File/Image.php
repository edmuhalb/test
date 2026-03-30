<?php

declare(strict_types=1);

namespace App\Services\File;

class Image
{
    public ?string $image;

    public function __construct(?string $image = null)
    {
        $this->image = $image;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }
}
