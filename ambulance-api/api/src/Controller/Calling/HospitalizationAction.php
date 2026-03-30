<?php

declare(strict_types=1);

namespace App\Controller\Calling;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Collections\NotesCollection;
use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Exceptions\AmoCRMMissedTokenException;
use AmoCRM\Exceptions\AmoCRMoAuthApiException;
use AmoCRM\Filters\LeadsFilter;
use AmoCRM\Helpers\EntityTypesInterface;
use AmoCRM\Models\LeadModel;
use AmoCRM\Models\NoteType\CommonNote;
use App\Entity\Calling\Calling;
use App\Flusher;
use App\Repository\CallingRepository;
use App\Services\AmoCRM;
use App\Services\BuhClient;
use App\Services\CallingSender;
use App\Services\HospitalizationScheduler;
use DateTimeImmutable;
use DateTimeZone;
use DomainException;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[AsController]
class HospitalizationAction extends AbstractController
{
    private AmoCRMApiClient $client;
    private CallingSender $sender;
    private HospitalizationScheduler $scheduler;

    public function __construct(
        AmoCRM $amoCRM,
        CallingSender $sender,
        HospitalizationScheduler $scheduler,
        private readonly BuhClient $buhClient,
    ) {
        $this->client = $amoCRM->getClient();
        $this->sender = $sender;
        $this->scheduler = $scheduler;
    }

    /**
     * @throws AmoCRMoAuthApiException
     * @throws AmoCRMApiException
     * @throws AmoCRMMissedTokenException
     */
    public function __invoke(Calling $calling, CallingRepository $callings, Flusher $flusher): JsonResponse
    {
        $filter = new LeadsFilter();
        $filter->setIds([$calling->getNumberCalling()]);

        $leads = $this->client->leads()->get($filter);

        if (!$leads) {
            throw new NotFoundHttpException('Не найден лид №' . $calling->getNumberCalling() . ' в AmoCRM');
        }

        $message = 'Информация от бригады' . PHP_EOL;
        $message .= 'Заявка №' . $calling->getNumberCalling() . PHP_EOL;
        $message .= $calling->getPrice() ? 'Итоговая цена ' . $calling->getPrice() . PHP_EOL : '';
        $message .= $calling->getCoastHospital() ? 'Стоимость госпитализации ' . $calling->getCoastHospital() . PHP_EOL : '';
        $message .= $calling->getFio() ? 'ФИО пациента ' . $calling->getFio() . PHP_EOL : '';
        $message .= $calling->getAge() ? 'Возраст пациента ' . $calling->getAge() . PHP_EOL : '';
        $message .= $calling->getPassport() ? 'Паспорт ' . $calling->getPassport() . PHP_EOL : '';
        $message .= $calling->getCostDay() ? 'Стоимость в сутки ' . $calling->getCostDay() . PHP_EOL : '';
        $message .= $calling->getNote() ? 'Примечание ' . $calling->getNote() . PHP_EOL : '';

        $currentDate = new DateTimeImmutable('now', new DateTimeZone('Europe/Moscow'));

        $entityId = null;

        /** @var LeadModel $lead */
        foreach ($leads as $lead) {
            $entityId = $lead->getId();
            $lead->setStatusId(45084664);
            $lead->setName($currentDate->format('d.m.y') . ' ' . $calling->getFio());
            $lead->setPrice($calling->getPrice());
        }

        $this->client->leads()->update($leads);

        $notesCollection = new NotesCollection();
        $messageNote = new CommonNote();
        $messageNote->setEntityId($entityId)
            ->setText($message)
            ->setCreatedBy(0);

        $notesCollection->add($messageNote);

        try {
            $leadNotesService = $this->client->notes(EntityTypesInterface::LEADS);
            $leadNotesService->add($notesCollection);
        } catch (AmoCRMApiException $e) {
        }

        $calling->setComplete(new DateTimeImmutable());
        $flusher->flush();

        try {
            $this->buhClient->send($calling);
        }catch (Exception $e) {

        }

        $this->sender->sendToAdmin(
            $calling,
            'Вызов N ' . $calling->getNumberCalling() . ' завершен',
            'Спасибо за работу!'
        );
        try {
            $this->scheduler->schedule($calling);

            $this->sender->sendToAdmin(
                $calling,
                'Вызов N ' . $calling->getNumberCalling(),
                'Создано назначение на стационар'
            );
        } catch (Exception $exception) {
            throw new DomainException('Ошибка создания госпитализации в AmoCRM: ' . $exception->getMessage());
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
            'admin' => $calling?->getAdmin() ?: [
                'id' => $calling->getAdmin()->getId(),
                'phone' => $calling->getAdmin()->getPhone(),
                'name' => $calling->getAdmin()->getName(),
            ],
            'doctor' => $calling?->getDoctor() ?: [
                'id' => $calling->getDoctor()->getId(),
                'phone' => $calling->getDoctor()->getPhone(),
                'name' => $calling->getDoctor()->getName(),
            ],
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
            'partner' => $calling->getPartner() ?: [
                'id' => $calling->getPartner()->getId(),
                'name' => $calling->getPartner()->getName(),
                'whatsappGroup' => $calling->getPartner()->getWhatsappGroup(),
            ],
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
            'client' => $calling->getClient() ?: [
                'id' => $calling->getClient()->getId(),
                'phone' => $calling->getClient()->getPhone(),
                'name' => $calling->getClient()->getName(),
            ],
            'noBusinessCards' => $calling->isCurrentNoBusinessCards(),
            'partnerHospitalization' => $calling->isCurrentPartnerHospitalization(),
            'images' => [],
            'addressInfo' => $calling->getAddressInfo(),
            'team' => $calling->getTeam() ?: [
                'id' => $calling->getTeam()->getId(),
                'status' => $calling->getTeam()->getStatus(),
                'phone' => $calling->getTeam()->getPhone() ?: [
                    'id' => $calling->getTeam()->getPhone()->getId(),
                    'externalId' => $calling->getTeam()->getPhone()->getExternalId(),
                ],
            ],
            'finishedAt' => $calling->getFinishedAt()?->format('d.m.Y H:i'),
            'repeat' => $calling->getCountRepeat(),
            'statusLabel' => $calling->getStatusLabel(),
        ], Response::HTTP_ACCEPTED);
    }
}
