<?php

declare(strict_types=1);

namespace App\UseCase\Call\SendFromCrm;

use App\Entity\Partner;

class Command
{
    private Lead $lead;
    private Contact $contact;
    private Partner $partner;

    public function __construct(Lead $lead, Contact $contact)
    {
        $this->lead = $lead;
        $this->contact = $contact;
    }

    public function getLead(): Lead
    {
        return $this->lead;
    }

    public function getContact(): Contact
    {
        return $this->contact;
    }
}
