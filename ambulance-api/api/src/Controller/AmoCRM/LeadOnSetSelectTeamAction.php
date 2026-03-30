<?php

declare(strict_types=1);

namespace App\Controller\AmoCRM;

use App\UseCase\Call\AddOrUpdateCall\Command;
use App\UseCase\Call\AddOrUpdateCall\Handler;
use Exception;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/amo-crm/lead-on-set-select-team', name: 'amo-crm.lead-on-set-select-team', methods: ['POST'])]
class LeadOnSetSelectTeamAction extends AbstractController
{
    public function __construct(
        private readonly Handler $handler,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $data = $request->request->all();

        try {
            $leadId = $this->getLeadId($data);
            $command = new Command($leadId);
            $this->handler->handle($command);
        } catch (Exception $e) {
            return $this->json([
                'error' => $e->getMessage(),
            ], Response::HTTP_OK);
        }

        return $this->json(null, Response::HTTP_OK);
    }

    private function getLeadId($data)
    {
        if (
            !isset($data['leads']) ||
            !isset($data['leads']['status']) ||
            !isset($data['leads']['status'][0]) ||
            !isset($data['leads']['status'][0]['id'])
        ) {
            throw new InvalidArgumentException('Отсутствует необходимое поле для получения ID заявки');
        }

        return $data['leads']['status'][0]['id'];
    }
}
