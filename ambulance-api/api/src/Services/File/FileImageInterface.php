<?php

declare(strict_types=1);

namespace App\Services\File;

interface FileImageInterface
{
    public function fileSave(Image $image, string $path): Image;

    public function fileDelete(Image $image, string $path): void;
}
