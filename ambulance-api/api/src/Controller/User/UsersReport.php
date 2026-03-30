<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Query\User\UsersReport\Fetcher;
use App\Query\User\UsersReport\Query;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class UsersReport extends AbstractController
{
    public function __construct(
        private readonly DenormalizerInterface $denormalizer,
        private readonly Fetcher $fetcher,
    ) {}

    /**
     * @throws Exception
     * @throws ExceptionInterface
     */
    #[Route(path: '/api/users/report', name: 'users_report', methods: 'GET', priority: 10)]
    public function __invoke(Request $request): JsonResponse
    {
        ini_set('memory_limit', '-1');

        $query = $this->denormalizer->denormalize($request->query->all(), Query::class);

        $result = $this->fetcher->fetch($query);

        return $this->json(array_values($result));
    }
}
