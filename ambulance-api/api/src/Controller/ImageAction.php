<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/uploads/{filename}', name: 'get_upload_image', methods: ['POST', 'GET'])]
class ImageAction extends AbstractController
{
    public function __invoke(string $filename, Request $request, KernelInterface $kernel): JsonResponse
    {
        $projectDir = $kernel->getProjectDir();
        $imageFile = $projectDir . '/uploads/' . $filename;
        $type = pathinfo($imageFile, PATHINFO_EXTENSION);
        $data = file_get_contents($imageFile);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

        return $this->json([
            'image' => $base64,
        ]);
    }
}
