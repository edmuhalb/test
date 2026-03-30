<?php

namespace App\EventSubscriber;

use App\Entity\Calling\AmbulanceCallLog;
use App\Entity\Calling\Calling;
use App\Entity\Calling\Status;
use App\Entity\MedTeam\MedTeam;
use App\Repository\UserRepository;
use App\Security\UserIdentity;
use DateTimeImmutable;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\Security\Core\Security;

class AmbulanceCallSubscriber implements EventSubscriber
{
    public function __construct(
        private Security $security,
        private readonly UserRepository $users,
    ) {
    }
    public function getSubscribedEvents(): array
    {
        return [
            Events::onFlush,
        ];
    }

    /**
     * @throws EntityNotFoundException
     */
    public function onFlush(OnFlushEventArgs $args): void
    {
        $em = $args->getObjectManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            if (!$entity instanceof Calling) {
                continue;
            }

            $changeSet = $uow->getEntityChangeSet($entity);
            $statusChanged = isset($changeSet['status']);
            $teamChanged = isset($changeSet['team']);

            if (!$statusChanged && !$teamChanged) {
                continue;
            }

            /** @var Status|null $oldStatus */
            $oldStatus = null;
            /** @var Status|null $oldStatus */
            $newStatus = null;

            if ($statusChanged) {
                [$oldStatus, $newStatus] = $changeSet['status'];
                if ($oldStatus?->getName() === $newStatus?->getName()) {
                    $statusChanged = false;
                }
            }else {
                $oldStatus = new Status($entity->getStatus());
                $newStatus = $oldStatus;
            }

            /** @var MedTeam|null $oldShift */
            $oldShift = null;
            /** @var MedTeam|null $shift */
            $shift = null;

            if ($teamChanged) {
                [$oldShift, $shift] = $changeSet['team'];
                if ($oldShift?->getId() === $shift?->getId()) {
                    $teamChanged = false;
                    $shift = $oldShift;
                }
            }else {
                $shift = $entity->getTeam();
            }


            if (!$statusChanged && !$teamChanged) {
                continue;
            }

            /** @var UserIdentity|null $userIdentity */
            $userIdentity = $this->security->getUser();

            $user = $userIdentity?->getId() ? $this->users->get($userIdentity->getId()) : null;


            $log = new AmbulanceCallLog(
                $entity,
                new DateTimeImmutable(),
                $user,
                $oldStatus,
                $newStatus,
                $shift,
                $entity->getReasonForCancellation(),
            );

            $em->persist($log);
            $classMetadata = $em->getClassMetadata(AmbulanceCallLog::class);
            $uow->computeChangeSet($classMetadata, $log);
        }
    }
}