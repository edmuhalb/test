<?php

declare(strict_types=1);

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * @internal
 */
abstract class AbstractTest extends ApiTestCase
{
    use RefreshDatabaseTrait;
    private ?string $token = null;

    protected function setUp(): void
    {
        self::bootKernel();
    }

    /**
     * @param mixed|null $token
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    protected function createClientWithCredentials(?string $token = null): Client
    {
        /** @var string $token */
        $token = $token ?: $this->getToken();

        return self::createClient([], ['headers' => ['authorization' => 'Bearer ' . $token]]);
    }

    /**
     * @param mixed $body
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    protected function getToken(array $body = []): string
    {
        if ($this->token) {
            return $this->token;
        }

        $response = self::createClient()->request('POST', '/api/login_check', ['json' => $body ?: [
            'phone' => '79000000000',
            'password' => 'secret',
        ]]);

        self::assertResponseIsSuccessful();

        $data = $response->toArray();

        $token = (string)$data['token'];

        $this->token = $token;

        return $token;
    }
}
