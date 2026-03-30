<?php

declare(strict_types=1);

namespace App\Controller\AmoCRM;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Exceptions\AmoCRMApiNoContentException;
use AmoCRM\Filters\LeadsFilter;
use AmoCRM\Models\CustomFieldsValues\MultitextCustomFieldValuesModel;
use AmoCRM\Models\LeadModel;
use AmoCRM\Models\Leads\Pipelines\Statuses\StatusModel;
use AmoCRM\Models\TagModel;
use App\Dto\Amo\Employee;
use App\Dto\Amo\Lead;
use App\Entity\Calling\Calling;
use App\Flusher;
use App\Repository\AmoCrmTokenRepository;
use App\Repository\CallingRepository;
use App\Repository\UserRepository;
use Carbon\Carbon;
use DateTimeImmutable;
use DomainException;
use League\OAuth2\Client\Token\AccessTokenInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/amo-crm/testt', name: 'amo-crm_testt', methods: ['GET'])]
class TesttAction extends AbstractController
{
    private AmoCRMApiClient $client;
    private CallingRepository $callings;
    private UserRepository $users;
    private Flusher $flusher;

    public function __construct(
        AmoCrmTokenRepository $tokens,
        CallingRepository $callings,
        UserRepository $users,
        Flusher $flusher
    ) {
        $apiClient = new AmoCRMApiClient(
            'd80b0f1f-1687-4b1e-8abd-9f3cbbe7a19e',
            'fCUzh7hiQ1bcuKQSrdJVp7Mnwnwi4b2vsK4W7yzhBCcumEkvRcHl3wX3hVxglhmK',
            'https://ambulance.rc-respect.ru/api/amo-crm/auth/callback'
        );

        $token = $tokens->getToken();

        $apiClient->setAccessToken($token)
            ->setAccountBaseDomain('af4040148.amocrm.ru')
            ->onAccessTokenRefresh(
                static function (AccessTokenInterface $accessToken, string $baseDomain) use ($tokens): void {
                    $tokens->update($accessToken, $baseDomain);
                }
            );

        $this->client = $apiClient;
        $this->callings = $callings;
        $this->users = $users;
        $this->flusher = $flusher;
    }

    public function __invoke(Request $request): JsonResponse
    {
        $lead = $this->getLeadInfo(20481239);

        $this->onSetTeam($lead);

        return $this->json([], Response::HTTP_OK);
    }

    private function getUsers(): void
    {
        $filter = new LeadsFilter();
        $filter->setPipelineIds([4105087]);
        $leadsService = $leads = $this->client->leads();
        $leads = $leadsService->get($filter);
        foreach ($leads as $lead) {
            $this->getAmoUser($lead);
        }
        while ($leads->getNextPageLink()) {
            $leads = $leadsService->nextPage($leads);
            foreach ($leads as $lead) {
                $this->getAmoUser($lead);
            }
        }
    }

    private function getAmoUser(LeadModel $lead): void
    {
        $badStatuses = [142, 143];
        if (\in_array($lead->getStatusId(), $badStatuses, true)) {
            return;
        }
        if (!$lead->getTags()) {
            return;
        }

        dump($lead->getId());
        dump($lead->getName());
        /** @var TagModel $tag */
        foreach ($lead->getTags() as $tag) {
            dump($tag->getId());
            dump($tag->getName());
        }
    }

    private function getTeams(): void
    {
        $pipeline = $this->client->pipelines()->getOne(4105087);
        $statuses = [38792953, 38816893, 142, 143];

        /** @var StatusModel $status */
        foreach ($pipeline->getStatuses() as $status) {
            if (\in_array($status->getId(), $statuses, true)) {
                continue;
            }
            $number = (int)preg_replace('/[^0-9]/', '', $status->getName());
            dump($status->getId());
            dump($status->getName());
            dump($number);
        }
    }

    private function getTeamUser(int $teamId): void
    {
        $filter = new LeadsFilter();

        $filter->setStatuses([[
            'pipeline_id' => 4105087,
            'status_id' => $teamId,
        ]]);

        $leads = $this->client->leads()->get($filter);

        /** @var LeadModel $lead */
        foreach ($leads as $lead) {
            dump($lead->getId());
        }
    }

    private function getLeadInfo(int $leadId): Lead
    {
        $lead = $this->client->leads()->getOne($leadId, [LeadModel::CONTACTS, LeadModel::CATALOG_ELEMENTS]);
        if (!$lead) {
            throw new DomainException('Не найден лид');
        }

        if (!$lead->getCustomFieldsValues()) {
            throw new DomainException('Не заполнены поля');
        }

        if (!$lead->getMainContact()) {
            throw new DomainException('Не указан контакт');
        }

        $contact = $this->client->contacts()->getOne($lead->getMainContact()->getId());

        if (!$contact) {
            throw new DomainException('Не найден контакт');
        }

        $name = $contact->getName();

        $phone = null;

        /** @var MultitextCustomFieldValuesModel $field */
        foreach ($contact->getCustomFieldsValues() as $field) {
            if ($field->getFieldId() === 604157) {
                $phone = $field->getValues()?->first()->getValue();
            }
        }

        if (!$phone) {
            throw new DomainException('Не найден телефон');
        }

        $leadDto = new Lead($leadId, $name, $phone);

        $leadDto->name = $lead->getName();

        foreach ($lead->getCustomFieldsValues() as $field) {
            if ($field->getFieldId() === 879807) {
                $leadDto->numberCalling = $field->getValues()?->first()->getValue();
            }
            if ($field->getFieldId() === 880453) {
                /** @var Carbon $dateTime */
                $leadDto->dateTime = $field->getValues()?->first()->getValue()?->toString();
                // $date_time = date("d.m.Y H:i:s", $one_field['values'][0]['value']);
            }
            if ($field->getFieldId() === 870903) {
                $leadDto->address = $field->getValues()?->first()->getValue();
            }

            if ($field->getFieldId() === 875863) {
                $leadDto->team = $field->getValues()?->first()->getValue();
            }
            if ($field->getFieldId() === 880527) {
                $leadDto->nosology = $field->getValues()?->first()->getValue();
            }

            if ($field->getFieldId() === 870907) {
                $leadDto->age = $field->getValues()?->first()->getValue();
            }

            if ($field->getFieldId() === 870945) {
                $leadDto->description = $field->getValues()?->first()->getValue();
            }

            if ($field->getFieldId() === 884333) {
                $leadDto->hz = $field->getValues()?->first()->getValue();
            }

            if ($field->getFieldId() === 960101) {
                $leadDto->leadType = $field->getValues()?->first()->getValue();
            }

            if ($field->getFieldId() === 882361) {
                $leadDto->partnerName = $field->getValues()?->first()->getValue();
            }
            if ($field->getFieldId() === 896921) {
                $leadDto->sendPhone = $field->getValues()?->first()->getValue();
            }
        }

        $teamId = $this->getTeamIdByNumber($leadDto->team);

        try {
            $filter = new LeadsFilter();

            $filter->setStatuses([[
                'pipeline_id' => 4105087,
                'status_id' => $teamId,
            ]]);

            $leads = $this->client->leads()->get($filter);
            /** @var LeadModel $lead */
            foreach ($leads as $lead) {
                /** @var TagModel $tag */
                foreach ($lead->getTags() as $tag) {
                    if ($tag->getId() === 62145) {
                        $leadDto->admin = new Employee($lead->getId(), $lead->getName(), 'ROLE_ADMIN');
                    }
                    if ($tag->getId() === 62135) {
                        $leadDto->doctor = new Employee($lead->getId(), $lead->getName(), 'ROLE_DOCTOR');
                    }
                }
            }
        } catch (AmoCRMApiNoContentException $e) {
            throw new DomainException('Ошибка получения персонала, Не сформирована бригада');
        }

        if (!$leadDto->doctor || !$leadDto->admin) {
            throw new DomainException('Не установлен персонал');
        }

        return $leadDto;
    }

    private function getTeamIdByNumber($number): int
    {
        $teams = [
            '1'=> ['Бригада 1', '38792956'],
            '2'=> ['Бригада 2', '38792959'],
            '3'=> ['Бригада 3', '38792962'],
            '4'=> ['Бригада 4', '38816761'],
            '5'=> ['Бригада 5', '38816764'],
            '6'=> ['Бригада 6', '38816767'],
            '7'=> ['Бригада 7', '42790108'],
            '8'=> ['Бригада 8', '42790111'],
            '9'=> ['Бригада 9', '42790114'],
            '10'=> ['Бригада 10', '42790117'],
            '11'=> ['Бригада 11', '42790120'],
            '12'=> ['Бригада 12', '53996154'],
            '13' => ['Бригада 13', '61367254'],
            '14' => ['Бригада 14', '61367258'],
            '15' => ['Бригада 15', '61417266'],
            '16' => ['Бригада 16', '61417270'],
        ];

        if (\array_key_exists($number, $teams)) {
            return (int)$teams[$number][1];
        }
        throw new DomainException('Нет бригады с таким номером');
    }

    private function onSetTeam(Lead $lead): void
    {
        $calling = $this->callings->findOneByNumber($lead->numberCalling);

        $admin = $this->users->getByExternalId($lead->admin->getId());
        $doctor = $this->users->getByExternalId($lead->doctor->getId());

        if ($calling) {
            if ($lead->dateTime) {
                $calling->setDateTime(new DateTimeImmutable($lead->dateTime));
            }

            $calling->setNosology($lead->nosology);
            $calling->setAge($lead->age);
            $calling->setChronicDiseases($lead->hz);
            $calling->setLeadType($lead->leadType);
            $calling->setPartnerName($lead->partnerName);
            $calling->setSendPhone($lead->sendPhone);

            $this->flusher->flush();
        } else {
            $calling = new Calling(
                $lead->numberCalling,
                $lead->name,
                $lead->clientName,
                $lead->clientPhone,
                $lead->address,
                $lead->description,
                $admin,
                $doctor
            );

            if ($lead->dateTime) {
                $calling->setDateTime(new DateTimeImmutable($lead->dateTime));
            }

            $calling->setNosology($lead->nosology);
            $calling->setAge($lead->age);
            $calling->setChronicDiseases($lead->hz);
            $calling->setLeadType($lead->leadType);
            $calling->setPartnerName($lead->partnerName);
            $calling->setSendPhone($lead->sendPhone);

            $this->callings->save($calling, true);
        }
    }
}
