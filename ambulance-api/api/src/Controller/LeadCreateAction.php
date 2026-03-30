<?php

declare(strict_types=1);

namespace App\Controller;

use App\UseCase\Call\AddForPartner\Command;
use App\UseCase\Call\AddForPartner\Handler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class LeadCreateAction extends AbstractController
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator,
        private readonly Handler $handler
    ) {}

    #[Route('/api/leads/{partnerId}', name: 'leads.create', methods: ['POST'])]
    public function calls(int $partnerId, Request $request): JsonResponse
    {
        $command = new Command($partnerId);

        $this->serializer->deserialize(
            $request->getContent(),
            Command::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $command]
        );

        $violations = $this->validator->validate($command);
        if (\count($violations)) {
            $json = $this->serializer->serialize($violations, 'json');
            return new JsonResponse($json, 424, [], true);
        }

        $this->handler->handle($command);

        return $this->json([], Response::HTTP_CREATED);
    }
}
