<?php

declare(strict_types=1);

namespace App\Entity\Calling;

use ApiPlatform\Metadata\ApiProperty;
use DateTimeImmutable;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class ExchangeCallCreateDto {

    #[Assert\NotNull]
    #[ApiProperty(
        description: 'ID партнера',
        example: 123,
        openapiContext: ['type' => 'integer']
    )]
    #[Groups(['exchange_calling:white'])]
    public ?int $partnerId = null;

    #[Assert\NotNull]
    #[ApiProperty(
        description: 'ID города (1 - Москва, 2 - Санкт-Петербург)',
        example: 1,
        openapiContext: ['type' => 'integer']
    )]
    #[Groups(['exchange_calling:white'])]
    public ?int $cityId = null;


    #[ApiProperty(
        description: 'Номер вызова',
        example: 'CALL-12345',
        openapiContext: ['type' => 'string']
    )]
    #[Assert\NotNull]
    #[Groups(['exchange_calling:white'])]
    public ?string $numberCall = null;

    #[ApiProperty(
        description: 'Номер вызова родителя',
        example: 'CALL-54321',
        openapiContext: ['type' => 'string']
    )]
    #[Groups(['exchange_calling:white'])]
    public ?string $parentNumberCall = null;


    #[ApiProperty(
        description: 'Имя клиента',
        example: 'Иванов Иван Иванович',
        openapiContext: ['type' => 'string']
    )]
    #[Assert\NotNull]
    #[Groups(['exchange_calling:white'])]
    public ?string $clientName = null;

    #[ApiProperty(
        description: 'Телефон клиента клиента',
        example: '79000000000',
        openapiContext: ['type' => 'string']
    )]
    #[Assert\NotNull]
    #[Groups(['exchange_calling:white'])]
    public ?string $clientPhone = null;

    #[ApiProperty(
        description: 'Адрес',
        example: 'г. Москва, Красная площадь, 9',
        openapiContext: ['type' => 'string']
    )]
    #[Assert\NotNull]
    #[Groups(['exchange_calling:white'])]
    public ?string $address = null;

    #[ApiProperty(
        description: 'Примечание к адресу',
        example: 'не шуметь, номер домофона 1235',
        openapiContext: ['type' => 'string']
    )]
    #[Groups(['exchange_calling:white'])]
    public ?string $addressInfo = null;


    #[ApiProperty(
        description: 'Комментарий к вызову',
        example: 'комментарий который оставляет КЦ для бригады',
    )]
    #[Groups(['exchange_calling:white'])]
    public ?string $description = null;

    #[ApiProperty(
        description: 'ФИО пациента',
        example: 'Петров Петр Петрович',
    )]
    #[Groups(['exchange_calling:white'])]
    public ?string $fio = null;

    #[Groups(['exchange_calling:white'])]
    public ?string $chronicDiseases = null;

    #[ApiProperty(
        description: 'Нозология',
    )]
    #[Groups(['exchange_calling:white'])]
    public ?string $nosology = null;

    #[Groups(['exchange_calling:white'])]
    public bool $sendPhone = false;

    #[ApiProperty(
        description: 'Дата и время, когда требуется бригада',
    )]
    #[Assert\NotNull]
    #[Groups(['exchange_calling:white'])]
    public ?DateTimeImmutable $dateTime = null;

    #[ApiProperty(
        description: 'Визитки - нет',
    )]
    #[Groups(['exchange_calling:white'])]
    public ?bool $noBusinessCards = false;

    #[ApiProperty(
        description: 'Госпитализация к партнеру',
    )]
    #[Groups(['exchange_calling:white'])]
    public ?bool $partnerHospitalization = false;

    #[ApiProperty(
        description: 'Персональная заявка',
    )]
    #[Groups(['exchange_calling:white'])]
    public ?bool $personal = false;

    #[ApiProperty(
        description: 'Не госпитализировать',
    )]
    #[Groups(['exchange_calling:white'])]
    public ?bool $doNotHospitalize = false;

    #[ApiProperty(
        description: 'День рождения',
    )]
    #[Groups(['exchange_calling:white'])]
    public ?DateTimeImmutable $birthday = null;
    #[ApiProperty(
        description: 'Координаты: долгота',
    )]
    #[Groups(['exchange_calling:white'])]
    #[Assert\NotNull]
    public $lon;
    #[ApiProperty(
        description: 'Координаты: широта',
    )]
    #[Groups(['exchange_calling:white'])]
    #[Assert\NotNull]
    public $lat;

    #[Groups(['exchange_calling:white'])]
    #[ApiProperty(
        description: 'Тип вызова: "general_profile" - Общий профиль, "narcology" - Наркология. Если не указан, то "narcology"',
        example: 'narcology',
        openapiContext: ['type' => 'string']
    )]
    public ?string $type = null;
}
