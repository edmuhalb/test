<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use ApiPlatform\Doctrine\Orm\Paginator;
use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\AdministratorReport;
use App\Entity\Calling\Calling;
use App\Entity\City;
use App\Entity\FileObject;
use App\Entity\Hospital\Clinic;
use App\Entity\Hospital\Hospital;
use App\Entity\MedTeam\MedTeam;
use App\Entity\Partner;
use App\Entity\PaymentSetting\PaymentSetting;
use App\Entity\Payroll\KpiDocument\KpiDocument;
use App\Entity\Payroll\PayrollCalculator;
use App\Entity\ReasonForCancellation;
use App\Entity\Service\Category;
use App\Entity\Service\Service;
use App\Entity\User\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class PaginateJsonSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['normalizePaginationResidentialComplex', EventPriorities::PRE_RESPOND],
        ];
    }

    public function normalizePaginationResidentialComplex(
        ViewEvent $event
    ): void {
        $method = $event->getRequest()->getMethod();
        if ($method !== Request::METHOD_GET) {
            return;
        }

        /** @var array $class */
        $class = $event->getRequest()->attributes->get('_route_params');

        if (!\array_key_exists('_api_resource_class', $class)) {
            return;
        }

        /** @var string $apiResourceClass */
        $apiResourceClass = $class['_api_resource_class'];

        if (
            $apiResourceClass !== City::class &&
            $apiResourceClass !== Partner::class &&
            $apiResourceClass !== Partner\PartnerUser::class &&
            $apiResourceClass !== Partner\Agreement\Agreement::class &&
            $apiResourceClass !== User::class &&
            $apiResourceClass !== Calling::class &&
            $apiResourceClass !== Category::class &&
            $apiResourceClass !== Service::class &&
            $apiResourceClass !== Hospital::class &&
            $apiResourceClass !== Clinic::class &&
            $apiResourceClass !== AdministratorReport::class &&
            $apiResourceClass !== FileObject::class &&
            $apiResourceClass !== PaymentSetting::class &&
            $apiResourceClass !== ReasonForCancellation::class &&
            $apiResourceClass !== KpiDocument::class &&
            $apiResourceClass !== PayrollCalculator::class &&
            $apiResourceClass !== MedTeam::class
        ) {
            return;
        }

        if (
            $apiResourceClass === MedTeam::class &&
            $event->getRequest()->attributes->get('_api_operation')?->getShortName() !== 'Shift'
        ) {
            return;
        }

        $data = $event->getRequest()->attributes->get('data');

        if ($data && $data instanceof Paginator) {
            $json = json_decode((string)$event->getControllerResult(), true);
            $pagination = [
                'first' => 1,
                'page' => $data->getCurrentPage(),
                'last' => $data->getLastPage(),
                'pages' => $data->getLastPage(),
                'next' => $data->getCurrentPage() + 1 > $data->getLastPage() ? $data->getLastPage() : $data->getCurrentPage() + 1,
                'totalItems' => $data->getTotalItems(),
                'itemsPerPage' => $data->getItemsPerPage(),
            ];

            $res = [
                'items' => $json,
                'pagination' => $pagination,
            ];
            $event->setControllerResult(json_encode($res));
        }
    }
}
