<?php

declare(strict_types=1);

namespace App\Controller\Calling;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Filters\LeadsFilter;
use AmoCRM\Models\LeadModel;
use App\Entity\Calling\Calling;
use App\Entity\Calling\Status;
use App\Flusher;
use App\Repository\CallingRepository;
use App\Repository\MedTeam\MedTeamRepository;
use App\Services\AmoCRM;
use App\Services\CallingSender;
use App\Services\TelegramSender;
use App\Services\TrackerToMkad;
use App\Services\WSClient;
use DateTimeImmutable;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/calls/{id}/set-team/{teamId}', name: 'call.set-team', methods: ['POST'])]
class SetTeamAction extends AbstractController
{
    private AmoCRMApiClient $client;

    public function __construct(
        private readonly CallingRepository $calls,
        private readonly MedTeamRepository $teams,
        private readonly Flusher $flusher,
        private readonly TrackerToMkad $trackerToMkad,
        private readonly CallingSender $sender,
        private readonly WSClient $wsClient,
        private readonly TelegramSender $tgSender,
        AmoCRM $amoCRM,
    ) {
        $this->client = $amoCRM->getClient();
    }

    public function __invoke($id, $teamId): JsonResponse
    {
        try {
            $call = $this->calls->getById($id);
            $team = $this->teams->getById($teamId);
            $call->setTeam($team);

            if ($call->isBuh()) {

                $this->handle($call);

                try {
                    $message = "‼️️️️ ВНИМАНИЕ ‼️\nУ вас новый вызов, зайдите в приложение";
                    $this->tgSender->send($call->getAdmin(), $message);
                    $this->tgSender->send($call->getDoctor(), $message);
                    $this->tgSender->send($call->getTeam()->getDriver(), $message);
                }catch (Exception $e) {
                }


                $this->sender->sendToAdmin(
                    $call,
                    'Внимание новый заказ',
                    $call->getAddress()
                );

                $this->wsClient->sendUpdateOffer($call->getId());

            }else {
                $filter = new LeadsFilter();
                $filter->setIds([$call->getNumberCalling()]);

                $leads = $this->client->leads()->get($filter);

                /** @var LeadModel $lead */
                foreach ($leads as $lead) {
                    $lead->setStatusId(38874646);
                }

                $this->client->leads()->update($leads);
            }

            $this->flusher->flush();
        } catch (Exception $exception) {
            return $this->json(['error' => $exception->getMessage(), $exception], Response::HTTP_ACCEPTED);
        }

        return $this->json([], Response::HTTP_ACCEPTED);
    }

    private function handle(Calling $call)
    {
        $call->setUpdatedAt(new DateTimeImmutable());

        $distance = $this->trackerToMkad->getDistance(
            (float)$call->getLat(),
            (float)$call->getLon(),
            $call->getCity()?->getId()
        );

        $call->setMkadDistance($distance);

        $call->setStatus(Status::assigned());
    }
}
