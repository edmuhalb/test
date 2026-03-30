<?php

declare(strict_types=1);

namespace App\Services\File;

use Symfony\Component\Filesystem\Filesystem;

class FileImageService implements FileImageInterface
{
    private int $depth = 3;

    public function __construct(
        private ?string $wordDir = null
    ) {}

    public function fileSave(Image $image, string $basePath): Image
    {
        if ($this->isBase64($image->getImage())) {
            $binaryData = $this->getBase64ImageFormat($image->getImage());
            $getPath = $this->getPath($this->wordDir . $basePath, $binaryData['mime']);

            $serverPath = $this->wordDir . $basePath . $getPath;
            $dbPath = $basePath . $getPath;

            file_put_contents($serverPath, $binaryData['image']);
            return new Image($dbPath);
        }
        return $image;
    }

    public function fileDelete(Image $image, string $path): void
    {
        $imagePath = $this->wordDir . $image->getImage();
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }

        $this->deleteDirectory($this->wordDir . $path);
    }

    public function getPath(string $basePath, ?string $format = null): string
    {
        $filesystem = new Filesystem();

        $format = $format ?: 'png';
        do {
            $path = '';
            for ($i = 0; $i < $this->depth; ++$i) {
                $path .= '/' . random_int(10, 99);
                if (!$filesystem->exists($basePath . $path)) {
                    $filesystem->mkdir($basePath . $path);
                }
            }
            $path .= '/';
        } while (is_file($basePath . $path));

        $randIntParseToString = (string)random_int(10000, 9999999);
        return $path . md5(md5($randIntParseToString)) . '.' . $format;
    }

    private function isBase64(string $string): bool
    {
        return strpos($string, 'base64') !== false;
    }

    private function getBase64ImageFormat($base64String)
    {
        $pattern = '/data:image\/([a-zA-Z]+);/';
        preg_match($pattern, $base64String, $matches);

        $base64Data = explode(';base64', $base64String);

        $decoded = base64_decode(end($base64Data), true);

        $mime = end($matches);

        return [
            'mime' => $mime,
            'image' => $decoded,
        ];
    }

    private function deleteDirectory(string $dir): bool
    {
        if (!file_exists($dir) || !is_dir($dir)) {
            return true;
        }

        $isEmpty = true;

        foreach (scandir($dir) as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $itemPath = $dir . \DIRECTORY_SEPARATOR . $item;

            if (is_dir($itemPath)) {
                if (!$this->deleteDirectory($itemPath)) {
                    $isEmpty = false;
                }
            } else {
                $isEmpty = false;
            }
        }

        if ($isEmpty) {
            return rmdir($dir);
        }

        return false;
    }
}
