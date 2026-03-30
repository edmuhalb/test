<?php

declare(strict_types=1);

namespace App\Controller\AmoCRM;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Exceptions\AmoCRMMissedTokenException;
use AmoCRM\Exceptions\AmoCRMoAuthApiException;
use AmoCRM\Filters\LeadsFilter;
use AmoCRM\Models\LeadModel;
use AmoCRM\Models\TagModel;
use App\Repository\UserRepository;
use App\Services\AmoCRM;
use DomainException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/amo-crm/get-team', name: 'amo-crm_get-team', methods: ['GET'])]
class GetTeamAction extends AbstractController
{
    private UserRepository $users;
    private AmoCRMApiClient $client;

    public function __construct(
        AmoCRM $amoCRM,
        UserRepository $users
    ) {
        $this->client = $amoCRM->getClient();
        $this->users = $users;
    }

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $user = $this->getUser();

            $lead = $this->client->leads()->getOne($user->getExternalId());

            $team = $this->getTeamById($lead?->getStatusId());

            $filter = new LeadsFilter();

            $filter->setStatuses([[
                'pipeline_id' => 4105087,
                'status_id' => $lead?->getStatusId(),
            ]]);

            $leads = $this->client->leads()->get($filter);
            /** @var LeadModel $lead */
            foreach ($leads as $lead) {
                /** @var TagModel $tag */
                foreach ($lead->getTags() as $tag) {
                    if ($tag->getId() === 62145) {
                        $user = $this->users->getByExternalId($lead->getId());
                        $team['admin'] = [
                            'id' => $user->getId(),
                            'name' => $user->getName(),
                            'phone' => $user->getPhone(),
                            'position' => $user->getPosition(),
                            'avatar' => $user->getAvatar(),
                            'roles' => $user->getRoles(),
                            'externalId' => $user->getExternalId(),
                        ];
                    }
                    if ($tag->getId() === 62135) {
                        $user = $this->users->getByExternalId($lead->getId());
                        $team['doctor'] = [
                            'id' => $user->getId(),
                            'name' => $user->getName(),
                            'phone' => $user->getPhone(),
                            'position' => $user->getPosition(),
                            'avatar' => $user->getAvatar(),
                            'roles' => $user->getRoles(),
                            'externalId' => $user->getExternalId(),
                        ];
                    }
                }
            }
            return $this->json($team, Response::HTTP_OK);
        } catch (AmoCRMApiException|AmoCRMMissedTokenException|AmoCRMoAuthApiException|DomainException $e) {
            return $this->json(null, Response::HTTP_OK);
        }
    }

    private function getTeamById($id): array
    {
        $teams = [
            '1' => ['Бригада 1', '38792956'],
            '2' => ['Бригада 2', '38792959'],
            '3' => ['Бригада 3', '38792962'],
            '4' => ['Бригада 4', '38816761'],
            '5' => ['Бригада 5', '38816764'],
            '6' => ['Бригада 6', '38816767'],
            '7' => ['Бригада 7', '42790108'],
            '8' => ['Бригада 8', '42790111'],
            '9' => ['Бригада 9', '42790114'],
            '10' => ['Бригада 10', '42790117'],
            '11' => ['Бригада 11', '42790120'],
            '12' => ['Бригада 12', '53996154'],
            '13' => ['Бригада 13', '61367254'],
            '14' => ['Бригада 14', '61367258'],
            '15' => ['Бригада 15', '61417266'],
            '16' => ['Бригада 16', '61417270'],
        ];

        foreach ($teams as $key => $team) {
            if ((int)$team[1] === (int)$id) {
                return [
                    'id' => (int)$key,
                    'external' => (int)$id,
                    'name' => $team[0],
                ];
            }
        }
        throw new DomainException('Нет бригады с таким внешним id');
    }
}
