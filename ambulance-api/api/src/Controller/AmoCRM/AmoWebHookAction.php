<?php

declare(strict_types=1);

namespace App\Controller\AmoCRM;

use App\Flusher;
use App\Repository\CallingRepository;
use App\Services\AmoCRM;
use App\Services\CallingSender;
use App\Services\TrackerToMkad;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/amo-crm/lead-update', name: 'amo-crm_lead-update', methods: ['POST'])]
class AmoWebHookAction extends AbstractController
{
    public function __construct(
        AmoCRM $amoCRM,
        private readonly CallingRepository $callings,
        private readonly Flusher $flusher,
        CallingSender $sender,
        TrackerToMkad $trackerToMkad
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $data = $request->request->all();
        // $test = '{"leads":{"update":[{"id":"20481239","name":"\u0422\u0435\u0441\u0442 \u0414\u043c\u0438\u0442\u0440\u0438\u0439","status_id":"38874646","old_status_id":"38307946","price":"0","responsible_user_id":"6784588","last_modified":"1679393193","modified_user_id":"6784588","created_user_id":"6784588","date_create":"1679388280","pipeline_id":"4018768","account_id":"29317822","custom_fields":[{"id":"960101","name":"\u0422\u0438\u043f \u0437\u0430\u044f\u0432\u043a\u0438","values":[{"value":"\u041d\u0430\u0448\u0430","enum":"650653"}]},{"id":"875587","name":"\u041f\u0435\u0440\u0432\u0438\u0447\u043d\u044b\u0439 \u0437\u0430\u043f\u0440\u043e\u0441","values":[{"value":"\u0412\u044b\u0432\u043e\u0434 \u0438\u0437 \u0437\u0430\u043f\u043e\u044f","enum":"529899"}]},{"id":"879807","name":"\u2116 \u0441\u0434\u0435\u043b\u043a\u0438","values":[{"value":"03.21-20481239"}]},{"id":"870901","name":"\u041a\u043e\u043c\u0443","values":[{"value":"\u0421\u0430\u043c\u043e\u043e\u0431\u0440\u0430\u0449\u0435\u043d\u0438\u0435","enum":"508745"}]},{"id":"870903","name":"\u0410\u0434\u0440\u0435\u0441","values":[{"value":"\u041b\u0430\u0432\u0440\u0443\u0448\u0438\u043d\u0441\u043a\u0438\u0439 \u043f\u0435\u0440\u0435\u0443\u043b\u043e\u043a, 10\u04414"}]},{"id":"870907","name":"\u0412\u043e\u0437\u0440\u0430\u0441\u0442","values":[{"value":"30"}]},{"id":"870909","name":"\u041f\u043e\u043b","values":[{"value":"\u041c","enum":"508749"}]},{"id":"870945","name":"\u041f\u0440\u0438\u043c\u0435\u0447\u0430\u043d\u0438\u0435","values":[{"value":"\u0422\u0443\u0442 \u043a\u0430\u043a\u043e\u0435 \u0442\u043e \u043e\u0433\u0440\u043e\u043c\u043d\u043e\u0435 \u043f\u0440\u0438\u043c\u0435\u0447\u0430\u043d\u0438\u0435...."}]},{"id":"875863","name":"\u0411\u0440\u0438\u0433\u0430\u0434\u0430","values":[{"value":"6","enum":"530185"}]},{"id":"896921","name":"\u041e\u0442\u043f\u0440\u0430\u0432\u0438\u0442\u044c \u0442\u0435\u043b\u0435\u0444\u043e\u043d","values":[{"value":"1"}]},{"id":"873879","name":"\u0410\u0434\u043c\u0438\u043d","values":[{"value":"\u0414\u0430\u0440\u044c\u044f \u0434\u0435\u0436\u0443\u0440\u043d\u044b\u0439 \u0430\u0434\u043c\u0438\u043d\u0438\u0441\u0442\u0440\u0430\u0442\u043e\u0440"}]},{"id":"873881","name":"\u0412\u0440\u0430\u0447","values":[{"value":"\u0422\u043a\u0430\u0447\u0451\u0432 \u0418\u0433\u043e\u0440\u044c"}]},{"id":"880453","name":"\u0414\u0430\u0442\u0430 \u0432\u0440\u0435\u043c\u044f \u043f\u0440\u0438\u0435\u0437\u0434\u0430","values":["1678355580"]},{"id":"882361","name":"\u041f\u0430\u0440\u0442\u043d\u0435\u0440","values":[{"value":"\u0421\u0430\u0439\u0442 \u041a\u043e\u0440\u0434\u0438\u044f","enum":"600689"}]}],"created_at":"1679388280","updated_at":"1679393193"}]},"account":{"subdomain":"af4040148","id":"29317822","_links":{"self":"https:\/\/af4040148.amocrm.ru"}}}';
        // $data = json_decode($test, true);

        $leadData = [];
        if (isset($data['leads']['update'][0])) {
            $leadData = $data['leads']['update'][0];
        }
        if (isset($data['leads']['add'][0])) {
            $leadData = $data['leads']['update'][0];
        }

        $customFields = $leadData['custom_fields'];

        if (!is_array($customFields)){
            return $this->json(null, Response::HTTP_OK);
        }

        $call = $this->callings->findOneByNumber($leadData['id']);
        if (!$call) {
            return $this->json(null, Response::HTTP_OK);
        }

        foreach ($customFields as $customField) {
            if ($customField['id'] == '971503') {

                if (isset($customField['values'][0]['value'])) {
                    $partnerComment = $customField['values'][0]['value'];
                    $call->setCommentForPartner($partnerComment);

                    $this->flusher->flush();
                    return $this->json(null, Response::HTTP_OK);
                }
            }
        }

        return $this->json(null, Response::HTTP_OK);
    }
}
