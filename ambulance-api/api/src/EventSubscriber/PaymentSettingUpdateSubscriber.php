<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\PaymentSetting\PaymentSetting;
use App\Entity\PaymentSetting\PaymentSettingVersion;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Events;

class PaymentSettingUpdateSubscriber implements EventSubscriber
{
    public function postUpdate(PostUpdateEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof PaymentSetting) {
            return;
        }

        $entityManager = $args->getObjectManager();

        $paymentSettingVersion = new PaymentSettingVersion(
            $entity,
            $entity->getValue()
        );

        $entityManager->persist($paymentSettingVersion);
        $entityManager->flush();
    }

    public function getSubscribedEvents()
    {
        return [
            Events::postUpdate,
        ];
    }
}
