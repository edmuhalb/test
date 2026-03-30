<?php

declare(strict_types=1);

namespace App\Controller\WorkSchedule;

use App\UseCase\WorkSchedule\Batch\Command;
use App\UseCase\WorkSchedule\Batch\Handler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BatchChange extends AbstractController
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator,
        private readonly Handler $handler,
    ) {}

    #[Route(path: '/api/work_schedules/batch', name: 'work_schedule_batch', methods: 'POST')]
    public function __invoke(Request $request): JsonResponse
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
