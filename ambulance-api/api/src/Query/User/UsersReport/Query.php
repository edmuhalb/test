<?php

declare(strict_types=1);

namespace App\Query\User\UsersReport;

class Query
{
    public ?string $period = null;
    public ?string $sort = 'name';
    public ?string $order = 'asc';
}
