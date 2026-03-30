<?php

declare(strict_types=1);

namespace App\Controller;

use App\UseCase\TouchToCall\Command;
use App\UseCase\TouchToCall\Handler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class AsteriskController extends AbstractController
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator,
        private readonly Handler $handler,
    ) {}

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/api/v1/touch-to-call', name: 'touch-to-call', methods: ['POST'])]
    public function touchToCall(Request $request, HttpClientInterface $client)
    {
        /** @var Command $command */
        $command = $this->serializer->deserialize(
            $request->getContent(),
            Command::class,
            'json'
        );

        $violations = $this->validator->validate($command);
        if (\count($violations)) {
            $json = $this->serializer->serialize($violations, 'json');
            return new JsonResponse($json, 424, [], true);
        }

        $this->handler->handle($command);

        return $this->json([]);
    }
}
