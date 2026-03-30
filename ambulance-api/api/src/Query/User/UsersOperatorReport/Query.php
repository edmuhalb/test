<?php

declare(strict_types=1);

namespace App\Query\User\UsersOperatorReport;

class Query
{
    public ?string $period = null;
    public ?string $sort = 'name';
    public ?string $order = 'asc';
}
