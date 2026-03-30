<?php

declare(strict_types=1);

namespace App\State\MedTeam;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\AdministratorReport;
use App\Entity\Calling\Status;
use App\Entity\MediaObject;
use App\Entity\MedTeam\MedTeam;
use App\Repository\AdministratorReportRepository;
use App\Services\File\UploadedBase64File;
use App\Services\MedTeam\EmployeeNotification;
use App\Services\Payroll\ShiftPayrollCalculator;
use App\Services\TelegramSender;
use App\Services\WSClient;
use Exception;
use Symfony\Component\HttpFoundation\RequestStack;

readonly class PostProcessor implements ProcessorInterface
{
    public function __construct(
        private ProcessorInterface $processor,
        private RequestStack $requestStack,
        private AdministratorReportRepository $reports,
        private WSClient $wsClient,
        private EmployeeNotification $employeeNotification,
        private TelegramSender $tgSender,
        private ShiftPayrollCalculator $calculator,
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $this->wsClient->sendUpdateTeam($data->getId());

        if ($data instanceof MedTeam && $data->isSendSms()) {
            $this->employeeNotification->send($data);
        }

        if ($data->getStatus() !== 'completed' && $data->getCompletedAt() !== null) {
            return $this->processor->process($data, $operation, $uriVariables, $context);
        }

        $request = $this->requestStack->getCurrentRequest();
        $requestData = $request->toArray();

        $mileageReceipts = $requestData['mileageReceipts'] ?? null;
        $parkingFeesReceipts = $requestData['parkingFeesReceipts'] ?? null;
        $tollRoadReceipts = $requestData['tollRoadReceipts'] ?? null;

        $mileage = $requestData['mileage'] ?? 0;
        $parkingFees = $requestData['parkingFees'] ?? 0;
        $tollRoad = $requestData['tollRoad'] ?? 0;

        if (!$mileageReceipts && !$parkingFeesReceipts && !$tollRoadReceipts && !$mileage && !$parkingFees && !$tollRoad) {
            return $this->processor->process($data, $operation, $uriVariables, $context);
        }

        $report = new AdministratorReport();

        $report->setTeam($data);

        if ($mileageReceipts && \is_array($mileageReceipts)) {
            foreach ($mileageReceipts as $key => $mileageReceipt) {
                $image = new MediaObject();
                $image->base64content = $mileageReceipt['base64content'] ?? null;
                if ($image->base64content) {
                    $imageFile = new UploadedBase64File($image->base64content, 'mileage_receipt-' . $key . '.png');
                    $image->file = $imageFile;
                    $report->addMileageReceipt($image);
                }
            }
        }

        if ($parkingFeesReceipts && \is_array($parkingFeesReceipts)) {
            foreach ($parkingFeesReceipts as $key => $parkingFeesReceipt) {
                $image = new MediaObject();
                $image->base64content = $parkingFeesReceipt['base64content'] ?? null;
                if ($image->base64content) {
                    $imageFile = new UploadedBase64File($image->base64content, 'parking_fees_receipt-' . $key . '.png');
                    $image->file = $imageFile;
                    $report->addParkingFeesReceipt($image);
                }
            }
        }

        if ($tollRoadReceipts && \is_array($tollRoadReceipts)) {
            foreach ($tollRoadReceipts as $key => $tollRoadReceipt) {
                $image = new MediaObject();
                $image->base64content = $tollRoadReceipt['base64content'] ?? null;
                if ($image->base64content) {
                    $imageFile = new UploadedBase64File($image->base64content, 'total_road_receipt-' . $key . '.png');
                    $image->file = $imageFile;
                    $report->addTollRoadReceipt($image);
                }
            }
        }

        $report->setMileage($mileage);
        $report->setParkingFees($parkingFees);
        $report->setToolRoad($tollRoad);

        $this->reports->save($report, true);

        if ($data instanceof MedTeam) {
            ini_set('memory_limit', '-1');
            // TODO вынести формирование отчета и отправку и переделать
            try {
                $messageAdmin = $this->buildReportMessage($data, $report, false);
            } catch (Exception $e) {
            }

            // try {
            //   $this->tgSender->send($data->getAdmin(), $messageAdmin);
            //
            // }catch (Exception $e) {}

            try {
                $this->tgSender->sendByRoleId(13, $messageAdmin);
            } catch (Exception $e) {
            }

            try {
                $messageDoctor = $this->buildReportMessage($data, $report, true);
            } catch (Exception $e) {
            }

            // try {
            //    $this->tgSender->send($data->getDoctor(), $messageDoctor);
            // }catch (Exception $e) {}

            try {
                $this->tgSender->sendByRoleId(13, $messageDoctor);
            } catch (Exception $e) {
            }

            $this->calculator->calculate($data);
        }

        return $this->processor->process($data, $operation, $uriVariables, $context);
    }

    public function buildReportMessage(MedTeam $data, ?AdministratorReport $report, bool $isDoctor): string
    {
        $message = [];

        $message[] = 'ОТЧЕТ ' . $data->getStartedAt()?->format('d.m.y') .
            ($data->getCity() ? ' г.' . $data->getCity()->getName() : '') . "\n";

        $message[] = 'НОМЕР СМЕНЫ ' . $data->getId() . "\n";
        $message[] = 'АДМИН: ' . $this->convertFio($data->getAdmin()->getName()) . "\n";
        $message[] = 'ВРАЧ: ' . $this->convertFio($data->getDoctor()->getName()) . "\n";
        if ($data->getDriver()) {
            $message[] = 'ВОДИТЕЛЬ: ' . $this->convertFio($data->getDriver()->getName()) . "\n";
        }

        $overTimeHoursReward = $data->getOverTimeHours() * 170;

        $message[] = 'ТИП СМЕНЫ ' . $data->getTypeTitle() . ' Сумма ' . ($isDoctor ? $data->getDoctorPrice() : $data->getAdminPrice()) . "\n";
        $message[] = 'ПЕРЕРАБОТКА ' . $overTimeHoursReward . "\n";
        $message[] = "\n";

        $calls = [];

        foreach ($data->getCallings() as $call) {
            if ($call->getStatus() === Status::COMPLETED) {
                $calls[] = $call;
            }
        }

        $message[] = 'ВЫЕЗДЫ: ' . \count($calls) . "\n";

        $callsAmount = 0;
        $callsReward = 0;
        $sewingIn = 0;
        $surchargeForPenalty = 0;

        if (\count($calls) > 0) {
            foreach ($calls as $key => $calling) {
                $amount = 0;
                $reward = 0;
                $surchargeForPenaltyCall = 0;

                $percent = 15;

                if (!$calling->isPersonal() && $calling->getCountRepeat() === 0) {
                    $percent = 10;
                }

                foreach ($calling->getServices() as $service) {
                    if (
                        !$service->isStationary() &&
                        !$service->isHospital() &&
                        !$service->isCoding() &&
                        !$service->getService()->isRepeatDesign() &&
                        $service->getService()->getId() !== 22 &&
                        $service->getService()->getId() !== 23
                    ) {
                        $amount += $service->getPrice();
                    }

                    if ($service->isCoding() && $service->getService()->getId() !== 16) {
                        $amount += $service->getPrice() - $service->getService()->getCoastPrice();
                    }

                    if ($service->isCoding() && $service->getService()->getId() === 16) {
                        $sewingIn += 2500;
                    }

                    if (
                        $service->getService()->getId() === 22 ||
                        $service->getService()->getId() === 23
                    ) {
                        if ($service->getPrice() > 500) {
                            $surchargeForPenaltyCall = 500;
                        }
                    }
                }

                $reward += $amount * $percent / 100;

                if ($surchargeForPenaltyCall > 0) {
                    $message[] = $key + 1 . '. ' . $this->convertFio($calling->getFio()) . ' id-' . $calling->getId() .
                        ' доплата за медотвод - ' . $surchargeForPenaltyCall . "\n";
                } else {
                    $message[] = $key + 1 . '. ' . $this->convertFio($calling->getFio()) . ' id-' . $calling->getId() .
                        ' ' . $amount . ' - ' . $percent . '% = ' . $reward . ' ' .
                        ($calling->getCountRepeat() > 0 ? ' П' : '') .
                        ($calling->isPersonal() ? ' И' : '') . "\n";
                }

                $surchargeForPenalty += $surchargeForPenaltyCall;
                $callsReward += $reward;
                $callsAmount += $amount;
            }

            $message[] = 'ИТОГО ВЫЗОВЫ: ' . $callsAmount . "\n";
            $message[] = 'ЗП Админ Выезды ' . $callsReward + $surchargeForPenalty . "\n";
            $message[] = 'ЗП Врач Выезды ' . $callsReward + $sewingIn + $surchargeForPenalty . "\n";
        }

        $message[] = "\n";

        $comboCount = 0;
        $comboMessage = [];
        $comboAmount = 0;

        // ************ КОМБО ************
        if ($isDoctor) {
            foreach ($calls as $calling) {
                if ($calling->isDoctorCombo()) {
                    ++$comboCount;
                    $comboMessage[] = $calling->getFio();
                    $therapySum = $calling->getTherapySum();
                    $comboMessage[] = $calling->getCompletedAt()?->format('d.m.y') . ' id-' . $calling->getId() . ' ' . $therapySum . '*2.5% = ' . $therapySum * 2.5 / 100 . "\n";
                    $owner = $calling->getOwner();
                    while ($owner) {
                        $therapySum = $owner->getTherapySum();
                        $comboMessage[] = $owner->getCompletedAt()?->format('d.m.y') . ' id-' . $owner->getId() . ' ' . $therapySum . '*2.5% = ' . $therapySum * 2.5 / 100 . "\n";
                        $owner = $owner->getOwner();
                    }
                }
            }
        } else {
            foreach ($calls as $calling) {
                if ($calling->isAdminCombo()) {
                    ++$comboCount;
                    $comboMessage[] = $calling->getFio();
                    $therapySum = $calling->getTherapySum();
                    $comboMessage[] = $calling->getCompletedAt()?->format('d.m.y') . ' id-' . $calling->getId() . ' ' . $therapySum . '*2.5% = ' . $therapySum * 2.5 / 100 . "\n";

                    $owner = $calling->getOwner();
                    while ($owner) {
                        $therapySum = $owner->getTherapySum();
                        $comboMessage[] = $owner->getCompletedAt()?->format('d.m.y') . ' id-' . $owner->getId() . ' ' . $therapySum . '*2.5% = ' . $therapySum * 2.5 / 100 . "\n";
                        $owner = $owner->getOwner();
                    }
                }
            }
        }

        $message[] = 'КОМБО: ' . $comboCount . "\n";

        if ($comboCount > 0) {
            $message = array_merge($message, $comboMessage);

            $message[] = 'ИТОГО КОМБО: ' . $comboAmount . "\n";
        }
        $message[] = "\n";

        // ************ Стационары ************

        $stationaryCount = 0;
        $stationaryMessage = [];
        $stationaryAmount = 0;
        $stationaryReward = 0;

        foreach ($calls as $calling) {
            $amount = 0;
            $reward = 0;
            $percent = 5;
            foreach ($calling->getServices() as $service) {
                if ($service->isStationary()) {
                    $amount += $service->getPrice();
                    $reward += $amount * 5 / 100;
                    ++$stationaryCount;
                    $stationaryMessage[] = $stationaryCount . '. ' . $this->convertFio($calling->getFio()) . ' id-' . $calling->getId()
                        . ' ' . $amount . ' - ' . $percent . '% = ' . $reward . "\n";
                }
            }

            $stationaryAmount += $amount;
            $stationaryReward += $reward;
        }

        $message[] = 'СТАЦИОНАР: ' . $stationaryCount . "\n";

        if ($stationaryCount > 0) {
            $message = array_merge($message, $stationaryMessage);

            $message[] = 'ИТОГО СТАЦ: ' . $stationaryAmount . "\n";
            $message[] = 'ЗП Админ Стац ' . $stationaryReward . "\n";
            $message[] = 'ЗП Врач Стац ' . $stationaryReward . "\n";
        }
        $message[] = "\n";

        // ************ Госпитализации ************

        $hospitalsCount = 0;
        $hospitalsMessages = [];
        $hospitalsAmount = 0;
        $hospitalsReward = 0;

        foreach ($calls as $calling) {
            $amount = 0;
            $reward = 0;
            $percent = 20;

            foreach ($calling->getServices() as $service) {
                if ($service->isHospital()) {
                    $amount += $service->getPrice();
                    $reward += $amount * $percent / 100;
                    ++$hospitalsCount;
                    $hospitalsMessages[] = $hospitalsCount . '. ' . $this->convertFio($calling->getFio()) . ' id-' . $calling->getId() .
                        ' ' . $amount . ' - ' . $percent . '% = ' . $reward . "\n";
                }
            }
            $hospitalsAmount += $amount;
            $hospitalsReward += $reward;
        }

        if ($hospitalsCount > 0) {
            $message[] = "ГОСПИТАЛИЗАЦИЯ:\n";
            $message = array_merge($message, $hospitalsMessages);
            $message[] = 'ИТОГО ГОСПИТ: ' . $hospitalsAmount . "\n";
            $message[] = 'ЗП Админ Госпит ' . $hospitalsReward . "\n";
            $message[] = 'ЗП Врач Госпит ' . $hospitalsReward . "\n";
            $message[] = "\n";
        }

        // ************ Госпитализации ************

        $mileage = $report?->getMileage() ? $report->getMileage() * 7.5 : 0;
        $toolRoad = $report?->getToolRoad() ?: 0;
        $parkingFees = $report?->getParkingFees() ?: 0;

        $transportAmount = $mileage + $toolRoad + $parkingFees;

        $message[] = "ТРАНСПОРТНЫЕ\n";
        $message[] = 'Пробег ' . $mileage . "\n";
        $message[] = 'Платка ' . $toolRoad . "\n";
        $message[] = 'Парковка ' . $parkingFees . "\n";
        $message[] = 'ИТОГО ТРАНСПОРТ: ' . $transportAmount . "\n";
        $message[] = "\n";

        // ************ Итоги ************

        $message[] = 'ВСЕГО ВЫРУЧКА ' . $callsAmount + $stationaryAmount + $hospitalsAmount . "\n";
        if ($isDoctor) {
            $message[] = 'ВСЕГО ЗП ВРАЧ ' .
                $data->getDoctorPrice() + $callsReward + $hospitalsReward + $stationaryReward + $overTimeHoursReward + $sewingIn + $surchargeForPenalty + $comboAmount . "\n";
        } else {
            $message[] = 'ВСЕГО ЗП АДМИН ' .
                $data->getDoctorPrice() + $callsReward + $hospitalsReward + $stationaryReward + $overTimeHoursReward + $surchargeForPenalty + $transportAmount + $comboAmount . "\n";
        }

        $result = implode('', $message);

        $result = mb_convert_encoding($result, 'UTF-8');

        return htmlspecialchars($result, ENT_QUOTES, 'UTF-8');
    }

    public function convertFio($fio)
    {
        if (empty($fio)) {
            return '-';
        }

        $parts = explode(' ', $fio);
        $result = $parts[0];

        if (\count($parts) > 1) {
            $str = mb_substr($parts[1], 0, 1);
            if (!empty($str)) {
                $result .= ' ' . $str . '.';
            }
        }

        if (\count($parts) > 2) {
            $str = mb_substr($parts[2], 0, 1);
            if (!empty($str)) {
                $result .= $str . '.';
            }
        }
        return $result;
    }
}
