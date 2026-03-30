<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\Calling\Calling;
use App\Entity\User\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Security;

final class CallingItemPhoneSubscriber implements EventSubscriberInterface
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['onKernelView', EventPriorities::PRE_VALIDATE],
        ];
    }

    public function onKernelView(ViewEvent $event): void
    {
        $call = $event->getControllerResult();
        $request = $event->getRequest();

        if (!$call instanceof Calling || !$request->isMethod('GET')) {
            return;
        }

        if ($request->attributes->get('_api_resource_class') !== Calling::class) {
            return;
        }

        if (null === $user = $this->security->getUser()) {
            return;
        }

        if (!$user instanceof User) {
            return;
        }

        $isGranted = $this->isGranted($user, 'calls-phone-full');

        if ($isGranted) {
            return;
        }

        $call->setPhone('+74951096650');
    }

    private function isGranted(User $user, string $role): bool
    {
        foreach ($user->getPermissions() as $permission) {
            if ($permission === $role) {
                return true;
            }
        }
        return false;
    }
}
