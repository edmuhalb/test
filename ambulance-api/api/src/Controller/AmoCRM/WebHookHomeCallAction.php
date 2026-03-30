<?php

declare(strict_types=1);

namespace App\Controller\AmoCRM;

use App\Entity\Calling\Calling;
use App\Flusher;
use App\Repository\CallingRepository;
use App\Repository\ClientRepository;
use App\Repository\PartnerRepository;
use App\Serializer\Call\CrmToAppDenormalizerInterface;
use App\Services\Call\CrmContactFetcher\CrmContactFetcherInterface;
use App\UseCase\Call\SendFromCrm\Command;
use App\UseCase\Call\SendFromCrm\Handler;
use App\UseCase\Call\SendFromCrm\Lead;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/amo-crm/web-hook-home-call', name: 'amo_crm.web_hook_home_call', methods: ['POST'])]
class WebHookHomeCallAction extends AbstractController
{
    public function __construct(
        private readonly CrmToAppDenormalizerInterface $leadDenormalizer,
        private readonly CrmContactFetcherInterface $contactFetcher,
        private readonly PartnerRepository $partners,
        private readonly ClientRepository $clients,
        private readonly ValidatorInterface $validator,
        private readonly Handler $handler,
        private readonly \App\UseCase\Partner\Create\Handler $partnerHandler,
        private readonly \App\UseCase\Client\Create\Handler $clientHandler,
        private readonly CallingRepository $calls,
        private readonly Flusher $flusher,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $data = $request->request->all();

        try {
            $lead = $this->leadDenormalizer->denormalize($data, Lead::class);

            $violations = $this->validator->validate($lead);
            if (\count($violations)) {
                return $this->json(null, Response::HTTP_OK);
            }

            if (!$lead->isPipelineHouseCall() || !$lead->isSuitableStatus()) {
                return $this->json(null, Response::HTTP_OK);
            }

            $call = $this->calls->findOneByNumber((string)$lead->getId());

            if (!$call) {
                $call = new Calling(
                    (string)$lead->getId(),
                    $lead->getName(),
                    '',
                    '',
                    $lead->address,
                    $lead->description,
                    null,
                    null
                );

                $owner = $this->calls->findOneByOwnerExternalId((string)$lead->getId());
                $call->setOwner($owner);

                $call->setFio($call->getOwner()?->getFio());
                $call->setAge($call->getOwner()?->getAge());

                $this->calls->add($call);
                $this->flusher->flush();
            }

            $partner = $this->partners->findOneByExternalId($lead->partnerExternalId);
            if (!$partner) {
                $partnerCommand = new \App\UseCase\Partner\Create\Command(
                    $lead->partnerName,
                    $lead->partnerExternalId
                );
                $this->partnerHandler->handle($partnerCommand);
            }

            $contact = $this->contactFetcher->fetch($lead->getId());

            $violations = $this->validator->validate($contact);
            if (\count($violations)) {
                return $this->json(null, Response::HTTP_OK);
            }

            $client = $this->clients->findByPhone($contact->getPhone());

            if (!$client) {
                $clientCommand = new \App\UseCase\Client\Create\Command(
                    $contact->getName(),
                    $contact->getPhone(),
                );
                $this->clientHandler->handle($clientCommand);
            }

            $command = new Command($lead, $contact);
            $this->handler->handle($command);
        } catch (Exception $e) {
            file_put_contents(
                \dirname(__DIR__) . '/../../var/error-1.txt',
                print_r($e->getMessage(), true) . PHP_EOL,
                FILE_APPEND
            );

            return $this->json(null, Response::HTTP_OK);
        }

        return $this->json(null, Response::HTTP_OK);
    }
}
