<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\AmoCrmToken;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use DomainException;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Token\AccessTokenInterface;

class AmoCrmTokenRepository
{
    private EntityRepository $repo;
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->repo = $em->getRepository(AmoCrmToken::class);
    }

    public function update(AccessTokenInterface $accessToken, string $baseDomain): void
    {
        /** @var AmoCrmToken $token */
        $token = $this->repo->createQueryBuilder('a')
            ->getQuery()
            ->getOneOrNullResult();

        if ($token) {
            $token->setAccessToken($accessToken->getToken());
            $token->setRefreshToken($accessToken->getRefreshToken());
            $token->setBaseDomain($baseDomain);
            $token->setExpires($accessToken->getExpires());
        } else {
            $token = new AmoCrmToken(
                $accessToken->getToken(),
                $accessToken->getRefreshToken(),
                $accessToken->getExpires(),
                $baseDomain
            );
            $this->em->persist($token);
        }
        $this->em->flush();
    }

    public function getToken(): AccessTokenInterface
    {
        /** @var AmoCrmToken $token */
        $token = $this->repo->createQueryBuilder('a')
            ->getQuery()
            ->getOneOrNullResult();

        if ($token) {
            return new AccessToken([
                'access_token' => $token->getAccessToken(),
                'refresh_token' => $token->getRefreshToken(),
                'expires' => $token->getExpires(),
                'baseDomain' => $token->getBaseDomain(),
            ]);
        }

        throw new DomainException('Access token not found');
    }
}
