<?php

declare(strict_types=1);

namespace App\Controller\PartnerApi;

use App\Security\PartnerUserIdentity;
use App\Repository\Partner\Agreement\AgreementRepository;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class PartnerAgreementAction extends AbstractController
{
    public function __construct(
        private readonly Security $security,
        private readonly AgreementRepository $agreementRepository,
        private readonly SerializerInterface $serializer,
    ) {}

    #[Route('/partner/agreement', name: 'partner-api.agreement.index', methods: ['GET'])]
    public function agreement(Request $request): JsonResponse
    {
        /** @var PartnerUserIdentity $user */
        $user = $this->security->getUser();
        if (!$user instanceof PartnerUserIdentity) {
            return $this->json(['error' => 'Partner not found'], 400);
        }

        $startsAt = new DateTimeImmutable();
        $agreement = $this->agreementRepository->findCurrentByPartnerId(
            $user->getPartnerId(),
            $startsAt
        );

        if (!$agreement) {
            return $this->json(['error' => 'Agreement not found'], 404);
        }

        $json = $this->serializer->serialize(
            $agreement,
            'json',
            [
                AbstractNormalizer::GROUPS => [
                    'agreement:read',
                    'agreement:item:read',
                    'partner:item:read',
                    'service-category:item:read',
                ],
            ]
        );

        return new JsonResponse($json, 200, [], true);
    }
}

