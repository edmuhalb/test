<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Security\PartnerUserIdentity;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;

final class JWTPartnerUsdrCreatedListener implements EventSubscriberInterface
{
    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function onJWTCreated(JWTCreatedEvent $event): void
    {
        $user = $event->getUser();

        if (!$user instanceof PartnerUserIdentity) {
            return;
        }

        $payload = $event->getData();
        $payload['id'] = $user->getId();
        $payload['name'] = $user->getName();
        $payload['roles'] = $user->getRoles();
        $payload['partner_id'] = $user->getPartnerId();
        $payload['partner_name'] = $user->getPartnerName();
        $event->setData($payload);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            Events::JWT_CREATED => 'onJWTCreated',
        ];
    }
}
