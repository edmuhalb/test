<?php

declare(strict_types=1);

namespace App\UseCase\Partner\Create;

use App\Entity\Partner;
use App\Flusher;
use App\Repository\Partner\Agreement\AgreementRepository;
use App\Repository\Partner\Agreement\AgreementTemplateRepository;
use App\Repository\PartnerRepository;
use DateTimeImmutable;
use Doctrine\ORM\NonUniqueResultException;
use DomainException;

class Handler
{
    public function __construct(
        private readonly PartnerRepository $partners,
        private readonly AgreementTemplateRepository $templates,
        private readonly AgreementRepository $agreements,
        private readonly Flusher $flusher,
    ) {}

    /**
     * @throws NonUniqueResultException
     */
    public function handle(Command $command): Partner
    {
        if ($command->getExternalId()) {
            $partner = $this->partners->findOneByExternalId($command->getExternalId());
            if ($partner) {
                throw new DomainException('Партнер с таким внешним id уже существует');
            }
        }

        $partner = new Partner();
        $partner->setExternalId($command->getExternalId());
        $partner->setName($command->getName());

        $agreement = new Partner\Agreement\Agreement();
        $agreement->setPartner($partner);
        $agreement->setStartsAt((new DateTimeImmutable())->setTime(0, 0));

        foreach ($this->templates->findAll() as $template) {
            $row = new Partner\Agreement\Row();
            $row->setAgreement($agreement);
            $row->setService($template->getService());
            $row->setDistance($template->getDistance());
            $row->setPercent($template->getPercent());
            $row->setRepeatNumber($template->getRepeatNumber());

            $agreement->addRow($row);
        }

        $this->partners->add($partner);
        $this->agreements->add($agreement);

        $this->flusher->flush();
        return $partner;
    }
}
