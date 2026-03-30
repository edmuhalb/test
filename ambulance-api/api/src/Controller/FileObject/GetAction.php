<?php

declare(strict_types=1);

namespace App\Controller\FileObject;

use App\Entity\FileObject;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class GetAction extends AbstractController
{
    public function __construct(
    ) {}

    public function __invoke(FileObject $fileObject): StreamedResponse
    {
        $projectDir = $this->getParameter('kernel.project_dir');
        $filePath = $projectDir . '/uploads/' . $fileObject->filePath;

        if (!file_exists($filePath)) {
            throw $this->createNotFoundException('File not found');
        }

        $response = new StreamedResponse(static function () use ($filePath): void {
            $handle = fopen($filePath, 'rb');
            fpassthru($handle);
            fclose($handle);
        });

        $response->headers->set('Content-Type', mime_content_type($filePath));
        $response->headers->set('Content-Disposition', 'inline; filename="' . basename($filePath) . '"');

        return $response;
    }
}
