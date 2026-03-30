<?php

declare(strict_types=1);

namespace App\Controller\MedTeam;

use App\Entity\MedTeam\MedTeam;
use App\Services\MedTeam\EmployeeNotification;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

#[AsController]
class SendSms extends AbstractController
{
    public function __construct(
        readonly private EmployeeNotification $employeeNotification
    ) {}

    /**
     * @throws TransportExceptionInterface
     */
    public function __invoke(MedTeam $medTeam): MedTeam
    {
        $this->employeeNotification->send($medTeam);

        return $medTeam;
    }
}
