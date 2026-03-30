<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Security\UserIdentity;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;

final class JWTCreatedListener implements EventSubscriberInterface
{
    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function onJWTCreated(JWTCreatedEvent $event): void
    {
        $user = $event->getUser();

        if ($user instanceof UserIdentity === false) {
            return;
        }

        $payload = $event->getData();
        $payload['id'] = $user->getId();
        $payload['name'] = $user->getName();
        $payload['position'] = $user->getPosition();
        $payload['avatar'] = $user->getAvatar();
        $payload['permissions'] = $user->getPermissions();

        $event->setData($payload);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            Events::JWT_CREATED => 'onJWTCreated',
        ];
    }
}
