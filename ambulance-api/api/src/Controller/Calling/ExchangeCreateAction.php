<?php

declare(strict_types=1);

namespace App\Controller\Calling;

use App\Entity\Calling\Calling;
use App\Entity\Calling\ExchangeCallCreateDto;
use App\Entity\Calling\Status;
use App\Entity\CallType;
use App\Entity\Client;
use App\Flusher;
use App\Repository\CallingRepository;
use App\Repository\CityRepository;
use App\Repository\ClientRepository;
use App\Repository\PartnerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;


//todo нельзя добавлять вызов с одинаковым внешним номером

#[AsController]
class ExchangeCreateAction extends AbstractController
{
    public function __construct(
        private readonly ValidatorInterface $validator,
        private readonly SerializerInterface $serializer,
    ) {
    }

    public function __invoke(
        ExchangeCallCreateDto $dto,
        CallingRepository $calls,
        PartnerRepository $partners,
        ClientRepository $clients,
        CityRepository $cities,
        Flusher $flusher
    ): JsonResponse
    {

        $violations = $this->validator->validate($dto);
        if (\count($violations)) {
            $json = $this->serializer->serialize($violations, 'json');
            return new JsonResponse($json, 424, [], true);
        }

        $partner = $partners->getById($dto->partnerId);

        $city = $cities->getById($dto->cityId);

        $client = $clients->findByPhone($dto->clientPhone);
        if (!$client) {
            $client = new Client(
                $dto->clientPhone,
                $dto->clientName
            );

            $clients->add($client);
        }

        $call = new Calling(
            $dto->numberCall,
            $dto->clientName,
            $dto->clientName,
            $dto->clientPhone,
            $dto->address,
            $dto->description
        );

        $call->setClient($client);
        $call->setPartner($partner);
        $call->setCity($city);

        $call->setAddressInfo($dto->addressInfo);
        $call->setFio($dto->fio);
        $call->setChronicDiseases($dto->chronicDiseases);
        $call->setNosology($dto->nosology);
        $call->setDateTime($dto->dateTime);
        $call->setSendPhone($dto->sendPhone);
        $call->setNoBusinessCards($dto->noBusinessCards);
        $call->setPartnerHospitalization($dto->partnerHospitalization);
        $call->setPersonal($dto->personal);
        $call->setDoNotHospitalize($dto->doNotHospitalize);
        $call->setBirthday($dto->birthday);
        $call->setBuh(true);
        $call->setLon($dto->lon);
        $call->setLat($dto->lat);
        $call->setType($dto->type);

        if ($dto->type && $dto->type !== CallType::NARCOLOGY && $dto->type !== CallType::GENERAL_PROFILE) {
           $dto->type = CallType::NARCOLOGY;
        }

        $call->setStatus(Status::waiting());

        if ($dto->parentNumberCall){
            $parentCall = $calls->findOneByNumber($dto->parentNumberCall);
            $call->setOwner($parentCall);
        }

        $calls->add($call);

        $flusher->flush();

        return $this->json($call, Response::HTTP_CREATED);
    }
}
