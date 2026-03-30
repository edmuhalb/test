<?php

declare(strict_types=1);

namespace App\Controller\AmoCRM;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Collections\CustomFieldsValuesCollection;
use AmoCRM\Collections\Leads\LeadsCollection;
use AmoCRM\Collections\NotesCollection;
use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Filters\LeadsFilter;
use AmoCRM\Helpers\EntityTypesInterface;
use AmoCRM\Models\CustomFieldsValues\TextCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\TextCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueModels\TextCustomFieldValueModel;
use AmoCRM\Models\NoteType\CommonNote;
use App\Dto\Amo\LeadForEmployee;
use App\Entity\MedTeam\MedTeam;
use App\Repository\MedTeam\MedTeamRepository;
use App\Services\AmoCRM;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/amo-crm/set-team', name: 'amo-crm_set-team', methods: ['POST'])]
class SetTeamAction extends AbstractController
{
    private AmoCRMApiClient $client;

    private MedTeamRepository $medTeamRepository;

    public function __construct(
        AmoCRM $amoCRM,
        MedTeamRepository $medTeamRepository
    ) {
        $this->client = $amoCRM->getClient();
        $this->medTeamRepository = $medTeamRepository;
    }

    public function __invoke(Request $request): JsonResponse
    {
        $data = $request->request->all();

        $leadId = $data['leads']['status'][0]['id'];

        $filter = new LeadsFilter();
        $filter->setIds([$leadId]);

        $leads = $this->client->leads()->get($filter);

        $leadData = $leads->first();

        $lead = $this->getLeadDto($leadData);

        $medTeam = $this->medTeamRepository->getLastWorkByNumber($lead->team);

        if (!$medTeam) {
            return $this->json(null, Response::HTTP_OK);
        }

        if (!$medTeam->getAdmin()) {
            return $this->json(null, Response::HTTP_OK);
        }

        if (!$medTeam->getDoctor()) {
            return $this->json(null, Response::HTTP_OK);
        }

        $message = $this->createMessage($lead, $medTeam);

        $this->sendMessageToAmo((int)$leadId, $message);

        $leadCustomFieldsValues = new CustomFieldsValuesCollection();

        $textCustomFieldValueModel = new TextCustomFieldValuesModel();
        $textCustomFieldValueModel->setFieldId(873881);
        $textCustomFieldValueModel->setValues(
            (new TextCustomFieldValueCollection())
                ->add((new TextCustomFieldValueModel())->setValue($medTeam->getDoctor()->getName()))
        );
        $leadCustomFieldsValues->add($textCustomFieldValueModel);

        $textCustomFieldValueModel = new TextCustomFieldValuesModel();
        $textCustomFieldValueModel->setFieldId(873879);
        $textCustomFieldValueModel->setValues(
            (new TextCustomFieldValueCollection())
                ->add((new TextCustomFieldValueModel())->setValue($medTeam->getAdmin()->getName()))
        );
        $leadCustomFieldsValues->add($textCustomFieldValueModel);

        $leadData->setCustomFieldsValues($leadCustomFieldsValues);
        $leadCollection = new LeadsCollection();

        $leadCollection->add($leadData);

        try {
            $this->client->leads()->update($leadCollection);
        } catch (AmoCRMApiException $e) {
            exit;
        }

        return $this->json(null, Response::HTTP_OK);
    }

    public function sendMessageToAmo($leadId, $message): void
    {
        $notesCollection = new NotesCollection();
        $messageNote = new CommonNote();
        $messageNote->setEntityId($leadId)
            ->setText($message)
            ->setCreatedBy(0);

        $notesCollection->add($messageNote);

        try {
            $leadNotesService = $this->client->notes(EntityTypesInterface::LEADS);
            $leadNotesService->add($notesCollection);
        } catch (AmoCRMApiException $e) {
        }
    }

    private function createMessage(LeadForEmployee $lead, MedTeam $team): string
    {
        $message = '';

        $message .= 'Заявка №: ' . $lead->numberCalling . PHP_EOL;
        $message .= 'Тип заявки: ' . $lead->leadType . PHP_EOL;

        $message .= 'Бригада №: ' . $lead->team . PHP_EOL;
        $message .= 'Сумма: ' . $lead->price . PHP_EOL . PHP_EOL;

        $message .= 'Врач: ' . $team->getDoctor()->getName() . PHP_EOL;
        $message .= 'Администратор: ' . $team->getAdmin()->getName() . PHP_EOL;
        $message .= 'Время прибытия: ' . $lead->dateTime . PHP_EOL . PHP_EOL;

        $message .= 'Адрес: ' . $lead->address . PHP_EOL . PHP_EOL;

        $message .= 'Нозология: ' . $lead->nosology . PHP_EOL;
        $message .= 'Возраст: ' . $lead->age . PHP_EOL;
        $message .= 'ХЗ: ' . $lead->hz . PHP_EOL . PHP_EOL;

        $message .= 'Примечание: ' . $lead->description . PHP_EOL;
        return $message;
    }

    private function getLeadDto($leadData): LeadForEmployee
    {
        $lead = new LeadForEmployee();

        $lead->price = $leadData->getPrice();

        foreach ($leadData->getCustomFieldsValues() as $field) {
            if ($field->getFieldId() === 879807) {
                $lead->numberCalling = $field->getValues()?->first()->getValue();
            }
            if ($field->getFieldId() === 880453) {
                $lead->dateTime = $field->getValues()?->first()->getValue()?->toString();
            }
            if ($field->getFieldId() === 870903) {
                $lead->address = $field->getValues()?->first()->getValue();
            }

            if ($field->getFieldId() === 875863) {
                $lead->team = $field->getValues()?->first()->getValue();
            }
            if ($field->getFieldId() === 880527) {
                $lead->nosology = $field->getValues()?->first()->getValue();
            }

            if ($field->getFieldId() === 870907) {
                $lead->age = $field->getValues()?->first()->getValue();
            }

            if ($field->getFieldId() === 870945) {
                $lead->description = $field->getValues()?->first()->getValue() ?: '';
            }

            if ($field->getFieldId() === 884333) {
                $lead->hz = $field->getValues()?->first()->getValue();
            }

            if ($field->getFieldId() === 960101) {
                $lead->leadType = $field->getValues()?->first()->getValue();
            }

            if ($field->getFieldId() === 896921) {
                $lead->sendPhone = $field->getValues()?->first()->getValue();
            }
        }

        return $lead;
    }
}
