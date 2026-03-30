<?php

declare(strict_types=1);

namespace App\Controller;

use Knp\Component\Pager\Pagination\PaginationInterface;

final class PaginationSerializer
{
    public static function toArray(PaginationInterface $pagination): array
    {
        $page = $pagination->getCurrentPageNumber();
        $last = ceil($pagination->getTotalItemCount() / $pagination->getItemNumberPerPage());
        return [
            'first' => 1,
            'page' => $page,
            'last' => $last,
            'pages' => $last,
            'next' => min($page + 1, $last),
            'totalItems' => $pagination->getTotalItemCount(),
            'itemsPerPage' => $pagination->getItemNumberPerPage(),
        ];
    }
}
