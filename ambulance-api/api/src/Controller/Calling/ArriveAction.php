<?php

declare(strict_types=1);

namespace App\Controller\Calling;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Filters\LeadsFilter;
use AmoCRM\Models\LeadModel;
use App\Entity\Calling\Calling;
use App\Entity\Calling\CallingArriveDto;
use App\Flusher;
use App\Repository\CallingRepository;
use App\Repository\TeamRepository;
use App\Services\AmoCRM;
use App\Services\BuhClient;
use App\Services\WSClient;
use DateTimeImmutable;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class ArriveAction extends AbstractController
{
    private AmoCRMApiClient $client;

    public function __construct(
        AmoCRM $amoCRM,
        private readonly WSClient $wsClient,
        private readonly BuhClient $buhClient,
    ) {
        $this->client = $amoCRM->getClient();
    }

    public function __invoke(
        Calling $calling,
        CallingArriveDto $dto,
        TeamRepository $teams,
        CallingRepository $callings,
        Flusher $flusher
    ): JsonResponse {
        $filter = new LeadsFilter();
        $filter->setIds([$calling->getNumberCalling()]);

        $leads = $this->client->leads()->get($filter);

        /** @var LeadModel $lead */
        foreach ($leads as $lead) {
            $lead->setStatusId(62358398);
        }

        $this->client->leads()->update($leads);

        $calling->setArrived(new DateTimeImmutable());

        $flusher->flush();

        $this->wsClient->sendUpdateOffer($calling->getId());

        try {
            $this->buhClient->send($calling);
        }catch (Exception $e) {

        }

        return $this->json([
            'id' => $calling->getId(),
            'title' => $calling->getTitle(),
            'name' => $calling->getName(),
            'phone' => '+70000000000',
            'fio' => $calling->getFio(),
            'numberCalling' => $calling->getNumberCalling(),
            'address' => $calling->getAddress(),
            'status' => $calling->getStatus(),
            'description' => $calling->getDescription(),
            'chronicDiseases' => $calling->getChronicDiseases(),
            'nosology' => $calling->getNosology(),
            'age' => $calling->getAge(),
            'leadType' => $calling->getLeadType(),
            'partnerName' => $calling->getPartnerName(),
            'sendPhone' => $calling->isSendPhone(),
            'rejectedComment' => $calling->getRejectedComment(),
            'createdAt' => $calling->getCreatedAt()?->format('d.m.Y H:i'),
            'updatedAt' => $calling->getUpdatedAt()?->format('d.m.Y H:i'),
            'acceptedAt' => $calling->getAcceptedAt()?->format('d.m.Y H:i'),
            'dispatchedAt' => $calling->getDispatchedAt()?->format('d.m.Y H:i'),
            'arrivedAt' => $calling->getArrivedAt()?->format('d.m.Y H:i'),
            'completedAt' => $calling->getCompletedAt()?->format('d.m.Y H:i'),
            'dateTime' => $calling->getDateTime(),
            'admin' => $calling?->getAdmin() !== null ? [
                'id' => $calling->getAdmin()->getId(),
                'phone' => $calling->getAdmin()->getPhone(),
                'name' => $calling->getAdmin()->getName(),
            ] : null,
            'doctor' => $calling?->getDoctor() !== null ? [
                'id' => $calling->getDoctor()->getId(),
                'phone' => $calling->getDoctor()->getPhone(),
                'name' => $calling->getDoctor()->getName(),
            ] : null,
            'price' => $calling->getPrice(),
            'estimated' => $calling->GetEstimated(),
            'prepayment' => $calling->getPrepayment(),
            'note' => $calling->getNote(),
            'passport' => $calling->getPassport(),
            'coastHospitalAdmission' => $calling->getCoastHospitalAdmission(),
            'coastHospital' => $calling->getCoastHospital(),
            'costDay' => $calling->getCostDay(),
            'phoneRelatives' => $calling->getPhoneRelatives(),
            'resultDate' => $calling->getResultDate(),
            'resultTime' => $calling->getResultTime(),
            'partner' => $calling->getPartner() !== null ? [
                'id' => $calling->getPartner()->getId(),
                'name' => $calling->getPartner()->getName(),
                'whatsappGroup' => $calling->getPartner()->getWhatsappGroup(),
            ] : null,
            'lon' => $calling->getLon(),
            'lat' => $calling->getLat(),
            'services' => [],
            'amount' => $calling->getAmount(),
            'paymentNextOrder' => $calling->getPaymentNextOrder(),
            'paymentHospitalization' => $calling->getPaymentHospitalization(),
            'totalAmount' => $calling->getTotalAmount(),
            'mkadDistance' => $calling->getMkadDistance(),
            'ownerExternalId' => $calling->getOwnerExternalId(),
            'operator' => null,
            'client' => $calling->getClient() !== null ? [
                'id' => $calling->getClient()->getId(),
                'phone' => $calling->getClient()->getPhone(),
                'name' => $calling->getClient()->getName(),
            ] : null,
            'noBusinessCards' => $calling->isCurrentNoBusinessCards(),
            'partnerHospitalization' => $calling->isCurrentPartnerHospitalization(),
            'images' => [],
            'addressInfo' => $calling->getAddressInfo(),
            'team' => $calling->getTeam() !== null ? [
                'id' => $calling->getTeam()->getId(),
                'status' => $calling->getTeam()->getStatus(),
                'phone' => $calling->getTeam()->getPhone() ?: [
                    'id' => $calling->getTeam()->getPhone()->getId(),
                    'externalId' => $calling->getTeam()->getPhone()->getExternalId(),
                ],
            ] : null,
            'finishedAt' => $calling->getFinishedAt()?->format('d.m.Y H:i'),
            'repeat' => $calling->getCountRepeat(),
            'statusLabel' => $calling->getStatusLabel(),
        ], Response::HTTP_ACCEPTED);
    }
}
