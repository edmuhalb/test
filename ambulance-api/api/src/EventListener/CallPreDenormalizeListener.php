<?php

declare(strict_types=1);

namespace App\EventListener;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Repository\CallingRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class CallPreDenormalizeListener implements EventSubscriberInterface
{
    private ?string $status = null;
    private array $services = [];

    public function __construct(
        private readonly CallingRepository $calls
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onPreDenormalize', EventPriorities::PRE_DESERIALIZE],
        ];
    }

    public function onPreDenormalize(RequestEvent $event): void
    {
        $request = $event->getRequest();

        $routeName = $request->get('_route');
        if ($routeName !== '_api_/api/v1/ambulance_calls/{id}{._format}_patch') {
            return;
        }

        $id =  $request->get('id');
        $call = $this->calls->getById($id);
        $this->status = $call->getStatus();
        $this->services = $call->getServices()->toArray();
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function getServices(): array
    {
        return $this->services;
    }
}
