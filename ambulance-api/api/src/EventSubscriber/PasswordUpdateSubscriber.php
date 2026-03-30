<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\User\User;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class PasswordUpdateSubscriber implements EventSubscriberInterface
{
    public function __construct(protected UserPasswordHasherInterface $passwordHasher) {}

    /**
     * @return array<string, string>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            BeforeEntityPersistedEvent::class => 'onBeforeEntityPersisted',
            BeforeEntityUpdatedEvent::class   => 'onBeforeEntityPersisted',
        ];
    }

    public function onBeforeEntityPersisted(BeforeEntityPersistedEvent|BeforeEntityUpdatedEvent $event): void
    {
        $entity = $event->getEntityInstance();

        if (!$entity instanceof User) {
            return;
        }

        if (null !== $entity->getPlainPassword() && '' !== $entity->getPlainPassword()) {
            $entity->setPassword(
                $this->passwordHasher->hashPassword($entity, $entity->getPlainPassword())
            );
        }
    }
}
