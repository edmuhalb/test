<?php

declare(strict_types=1);

namespace App\Controller\PartnerApi;

use App\Security\PartnerUserIdentity;
use App\Services\AmbulanceApiClient;
use App\UseCase\Call\AddForPartner\Command;
use App\UseCase\Call\AddForPartner\Handler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;

class PartnerCallsCreateAction extends AbstractController
{
    private const PER_PAGE = 50;

    public function __construct(
        private readonly Security $security,
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator,
        private readonly Handler $handler,
        private readonly AmbulanceApiClient $ambulanceApiClient
    ) {}

    #[Route('/partner/calls', name: 'partner-api.calls.create', methods: ['POST'])]
    public function calls(Request $request): JsonResponse
    {
        /** @var PartnerUserIdentity $user */
        $user = $this->security->getUser();
        if (!$user instanceof PartnerUserIdentity) {
            return $this->json(['error' => 'Partner not found'], 400);
        }

        $command = new Command($user->getPartnerId());

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

        //$this->handler->handle($command);

        // Отправляем POST запрос в Ambulance API
        try {
            $this->ambulanceApiClient->requestAndGetResponse(
                'createlead',
                [],
                'POST',
                [
                    'note' => $command->description,
                    'phone' => $command->phone,
                    'utm_source' => (string) $command->partnerId,
                    'type' => $command->type,
                    'client' => $command->client
                ]
            );
        } catch (HttpExceptionInterface $e) {
            // Логируем ошибку, но не прерываем выполнение
            // Можно добавить логирование здесь при необходимости
        }

        return $this->json([], Response::HTTP_CREATED);
    }
}
