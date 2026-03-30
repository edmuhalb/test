<?php

declare(strict_types=1);

namespace App\Command\Role\Permission;

use App\Entity\Role\Permission;
use App\Flusher;
use App\Repository\Role\PermissionRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'role:permission:init',
    description: 'Обновление разрешений системы доступа',
)]
class InitCommand extends Command
{
    private $data = [
        // Расчет зп
        // Метрики
        [
            'id' => 'payroll-metrics-view',
            'description' => 'Расчет ЗП Метрики: просмотр',
        ],
        [
            'id' => 'payroll-metrics-update',
            'description' => 'Расчет ЗП Метрики: изменение',
        ],
        // KPI
        [
            'id' => 'payroll-kpis-view',
            'description' => 'Расчет ЗП KPI: просмотр',
        ],
        [
            'id' => 'payroll-kpis-update',
            'description' => 'Расчет ЗП KPI: изменение',
        ],
        // Транспортные
        [
            'id' => 'payroll-transports-view',
            'description' => 'Расчет ЗП Транспортные: просмотр',
        ],
        [
            'id' => 'payroll-transports-update',
            'description' => 'Расчет ЗП Транспортные: изменение',
        ],

        // вызовы
        [
            'id' => 'calls-index',
            'description' => 'Вызовы: просмотр списка',
        ],
        [
            'id' => 'calls-create',
            'description' => 'Вызовы: добавление',
        ],
        [
            'id' => 'calls-view',
            'description' => 'Вызовы: просмотр детально',
        ],
        [
            'id' => 'calls-edit',
            'description' => 'Вызовы: изменение',
        ],
        [
            'id' => 'calls-delete',
            'description' => 'Вызовы: удаление',
        ],
        [
            'id' => 'calls-index-owner',
            'description' => 'Вызовы: просмотр списка своих заказов',
        ],
        [
            'id' => 'calls-view-owner',
            'description' => 'Вызовы: просмотр детально своих заказов',
        ],
        [
            'id' => 'calls-address-full',
            'description' => 'Вызовы: просмотр полного адреса',
        ],
        [
            'id' => 'calls-phone-full',
            'description' => 'Вызовы: просмотр полного телефона',
        ],
        // Партнеры
        [
            'id' => 'partners-index',
            'description' => 'Партнеры: просмотр списка',
        ],
        [
            'id' => 'partners-create',
            'description' => 'Партнеры: добавление',
        ],
        [
            'id' => 'partners-view',
            'description' => 'Партнеры: просмотр детально',
        ],
        [
            'id' => 'partners-view-calls',
            'description' => 'Партнеры: просмотр вызовов',
        ],
        [
            'id' => 'partners-view-hospital',
            'description' => 'Партнеры: просмотр стационаров',
        ],
        [
            'id' => 'partners-edit',
            'description' => 'Партнеры: изменение',
        ],
        [
            'id' => 'partners-delete',
            'description' => 'Партнеры: удаление',
        ],
        [
            'id' => 'partners-report',
            'description' => 'Партнеры: скачать отчет',
        ],
        [
            'id' => 'partners-agreements-index',
            'description' => 'Партнеры/Соглашения: просмотр списка',
        ],
        [
            'id' => 'partners-agreements-create',
            'description' => 'Партнеры/Соглашения: добавление',
        ],
        [
            'id' => 'partners-agreements-view',
            'description' => 'Партнеры/Соглашения: просмотр детально',
        ],
        [
            'id' => 'partners-agreements-update',
            'description' => 'Партнеры/Соглашения: изменение',
        ],
        [
            'id' => 'partners-agreements-delete',
            'description' => 'Партнеры/Соглашения: удаление',
        ],

        [
            'id' => 'partners-users_access-index',
            'description' => 'Партнеры/Пользователи партнеров: просмотр списка',
        ],
        [
            'id' => 'partners-users_access-create',
            'description' => 'Партнеры/Пользователи партнеров: добавление',
        ],
        [
            'id' => 'partners-users_access-view',
            'description' => 'Партнеры/Пользователи партнеров: просмотр детально',
        ],
        [
            'id' => 'partners-users_access-update',
            'description' => 'Партнеры/Пользователи партнеров: изменение',
        ],
        [
            'id' => 'partners-users_access-delete',
            'description' => 'Партнеры/Пользователи партнеров: удаление',
        ],

        // Пользователи
        [
            'id' => 'users-index',
            'description' => 'Пользователи: просмотр списка',
        ],
        [
            'id' => 'users-create',
            'description' => 'Пользователи: добавление',
        ],
        [
            'id' => 'users-view',
            'description' => 'Пользователи: просмотр детально',
        ],
        [
            'id' => 'users-edit',
            'description' => 'Пользователи: изменение',
        ],
        [
            'id' => 'users-delete',
            'description' => 'Пользователи: удаление',
        ],
        [
            'id' => 'users-report-index',
            'description' => 'Пользователи: просмотр полного отчета',
        ],
        // Карта
        [
            'id' => 'maps-index',
            'description' => 'Карта: просмотр карты',
        ],
        // Бригады
        [
            'id' => 'med_teams-index',
            'description' => 'Бригады: просмотр списка',
        ],
        [
            'id' => 'med_teams-create',
            'description' => 'Бригады: добавление',
        ],
        [
            'id' => 'med_teams-view',
            'description' => 'Бригады: просмотр детально',
        ],
        [
            'id' => 'med_teams-edit',
            'description' => 'Бригады: изменение',
        ],
        [
            'id' => 'med_teams-delete',
            'description' => 'Бригады: удаление',
        ],
        // Стационар
        [
            'id' => 'hospitals-index',
            'description' => 'Стационар: просмотр списка',
        ],
        [
            'id' => 'hospitals-create',
            'description' => 'Стационар: добавление',
        ],
        [
            'id' => 'hospitals-view',
            'description' => 'Стационар: просмотр детально',
        ],
        [
            'id' => 'hospitals-edit',
            'description' => 'Стационар: изменение',
        ],
        [
            'id' => 'hospitals-delete',
            'description' => 'Стационар: удаление',
        ],
        // Роли
        [
            'id' => 'roles-index',
            'description' => 'Роли: просмотр списка',
        ],
        [
            'id' => 'roles-create',
            'description' => 'Роли: добавление',
        ],
        [
            'id' => 'roles-view',
            'description' => 'Роли: просмотр детально',
        ],
        [
            'id' => 'roles-edit',
            'description' => 'Роли: изменение',
        ],
        [
            'id' => 'roles-delete',
            'description' => 'Роли: удаление',
        ],
        // Разрешения
        [
            'id' => 'permissions-index',
            'description' => 'Разрешения: просмотр списка',
        ],
        // Шаблоны соглашений
        [
            'id' => 'agreement_templates-index',
            'description' => 'Шаблоны соглашений: просмотр списка',
        ],
        [
            'id' => 'agreement_templates-create',
            'description' => 'Шаблоны соглашений: добавление',
        ],
        [
            'id' => 'agreement_templates-view',
            'description' => 'Шаблоны соглашений: просмотр детально',
        ],
        [
            'id' => 'agreement_templates-edit',
            'description' => 'Шаблоны соглашений: изменение',
        ],
        [
            'id' => 'agreement_templates-delete',
            'description' => 'Шаблоны соглашений: удаление',
        ],
        // Услуги
        [
            'id' => 'services-index',
            'description' => 'Услуги: просмотр списка',
        ],
        [
            'id' => 'services-create',
            'description' => 'Услуги: добавление',
        ],
        [
            'id' => 'services-view',
            'description' => 'Услуги: просмотр детально',
        ],
        [
            'id' => 'services-edit',
            'description' => 'Услуги: изменение',
        ],
        [
            'id' => 'services-delete',
            'description' => 'Услуги: удаление',
        ],
        // Категории услуг
        [
            'id' => 'service_categories-index',
            'description' => 'Категории услуг: просмотр списка',
        ],
        [
            'id' => 'service_categories-create',
            'description' => 'Категории услуг: добавление',
        ],
        [
            'id' => 'service_categories-view',
            'description' => 'Категории услуг: просмотр детально',
        ],
        [
            'id' => 'service_categories-edit',
            'description' => 'Категории услуг: изменение',
        ],
        [
            'id' => 'service_categories-delete',
            'description' => 'Категории услуг: удаление',
        ],
        // Телефонные аппараты
        [
            'id' => 'phones-index',
            'description' => 'Телефонные аппараты: просмотр списка',
        ],
        [
            'id' => 'phones-create',
            'description' => 'Телефонные аппараты: добавление',
        ],
        [
            'id' => 'phones-view',
            'description' => 'Телефонные аппараты: просмотр детально',
        ],
        [
            'id' => 'phones-edit',
            'description' => 'Телефонные аппараты: изменение',
        ],
        [
            'id' => 'phones-delete',
            'description' => 'Телефонные аппараты: удаление',
        ],
        // Базы
        [
            'id' => 'bases-index',
            'description' => 'Базы: просмотр списка',
        ],
        [
            'id' => 'bases-create',
            'description' => 'Базы: добавление',
        ],
        [
            'id' => 'bases-view',
            'description' => 'Базы: просмотр детально',
        ],
        [
            'id' => 'bases-edit',
            'description' => 'Базы: изменение',
        ],
        [
            'id' => 'bases-delete',
            'description' => 'Базы: удаление',
        ],
        // Автомобили
        [
            'id' => 'cars-index',
            'description' => 'Автомобили: просмотр списка',
        ],
        [
            'id' => 'cars-create',
            'description' => 'Автомобили: добавление',
        ],
        [
            'id' => 'cars-view',
            'description' => 'Автомобили: просмотр детально',
        ],
        [
            'id' => 'cars-edit',
            'description' => 'Автомобили: изменение',
        ],
        [
            'id' => 'cars-delete',
            'description' => 'Автомобили: удаление',
        ],

        // Настройки
        [
            'id' => 'settings-index',
            'description' => 'Настройки: просмотр списка',
        ],
        [
            'id' => 'settings-create',
            'description' => 'Настройки: добавление',
        ],
        [
            'id' => 'settings-view',
            'description' => 'Настройки: просмотр детально',
        ],
        [
            'id' => 'settings-edit',
            'description' => 'Настройки: изменение',
        ],
        [
            'id' => 'settings-delete',
            'description' => 'Настройки: удаление',
        ],

        // профессиональные навыки
        [
            'id' => 'can_be-admin',
            'description' => 'Может быть администратором бригады',
        ],
        [
            'id' => 'can_be-doctor',
            'description' => 'Может быть доктором бригады',
        ],
        [
            'id' => 'can_be-driver',
            'description' => 'Может быть водителем бригады',
        ],

        [
            'id' => 'can_be-cmp_reanimator',
            'description' => 'Может быть реаниматологом бригады СМП',
        ],
        [
            'id' => 'can_be-smp_paramedic',
            'description' => 'Может быть фельдшером бригады СМП',
        ],
        [
            'id' => 'can_be-smp_doctor',
            'description' => 'Может быть врачом бригады СМП',
        ],
        [
            'id' => 'can_be-smp_driver',
            'description' => 'Может быть водителем бригады СМП',
        ],


        // Отчеты администратора
        [
            'id' => 'administrator_reports-index',
            'description' => 'Отчеты администратора: просмотр списка',
        ],
        [
            'id' => 'administrator_reports-create',
            'description' => 'Отчеты администратора: добавление',
        ],
        [
            'id' => 'administrator_reports-view',
            'description' => 'Отчеты администратора: просмотр детально',
        ],
        [
            'id' => 'administrator_reports-edit',
            'description' => 'Отчеты администратора: изменение',
        ],
        [
            'id' => 'administrator_reports-delete',
            'description' => 'Отчеты администратора: удаление',
        ],

        // Страница логиста
        [
            'id' => 'logistics-index',
            'description' => 'Интерфейс логиста: просмотр',
        ],
        [
            'id' => 'logistics-set_team',
            'description' => 'Интерфейс логиста: назначение бригады',
        ],
    ];

    public function __construct(
        private readonly PermissionRepository $permissions,
        private readonly Flusher $flusher,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        foreach ($this->data as $dto) {
            $permission = $this->permissions->find($dto['id']);
            if (!$permission) {
                $permission = new Permission(
                    $dto['id'],
                    $dto['description']
                );
                $this->permissions->add($permission);
            } else {
                $permission->setDescription($dto['description']);
            }
        }
        $this->flusher->flush();

        $io->success('Разрешения в системе доступа обновлены.');

        return Command::SUCCESS;
    }
}
