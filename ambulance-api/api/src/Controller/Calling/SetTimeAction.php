<?php

declare(strict_types=1);

namespace App\Controller\Calling;

use App\UseCase\Call\SetTime\Command;
use App\UseCase\Call\SetTime\Handler;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/calls/{id}/set-time', name: 'call.set-time', methods: ['POST'])]
class SetTimeAction extends AbstractController
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator,
        private readonly Handler $handler,
    ) {}

    public function __invoke($id, Request $request): JsonResponse
    {
        $command = new Command((int)$id);

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

        try {
            $this->handler->handle($command);
        } catch (Exception $e) {
            return $this->json([
                'error' =>  $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json($command, Response::HTTP_ACCEPTED);
    }
}
