<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\Partner\PartnerUser;
use App\Repository\PartnerUserRepository;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

final class PartnerUserProvider implements UserProviderInterface
{
    public function __construct(
        private readonly PartnerUserRepository $partnerUserRepository,
    ) {
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $partnerUser = $this->partnerUserRepository->findOneBy(['phone' => $identifier]);

        if (!$partnerUser instanceof PartnerUser) {
            throw new UserNotFoundException(sprintf('Partner user with phone "%s" not found.', $identifier));
        }

        return $this->identityByPartnerUser($partnerUser);
    }

    public function refreshUser(UserInterface $user): PartnerUserIdentity
    {
        if (!$user instanceof PartnerUserIdentity) {
            throw new UnsupportedUserException(sprintf('Invalid user class "%s".', $user::class));
        }

        $partnerUser = $this->partnerUserRepository->findOneBy(['phone' => $user->getUserIdentifier()]);

        if (!$partnerUser instanceof PartnerUser) {
            throw new UserNotFoundException(sprintf('Partner user with phone "%s" not found.', $user->getUserIdentifier()));
        }

        return $this->identityByPartnerUser($partnerUser);
    }

    public function supportsClass(string $class): bool
    {
        return PartnerUserIdentity::class === $class || is_subclass_of($class, PartnerUserIdentity::class);
    }

    private function identityByPartnerUser(PartnerUser $partnerUser): PartnerUserIdentity
    {
        $partner = $partnerUser->getPartner();
        if ($partner === null) {
            throw new UserNotFoundException('Partner user has no associated partner.');
        }

        return new PartnerUserIdentity(
            (int) $partnerUser->getId(),
            (string) $partnerUser->getPhone(),
            (string) $partnerUser->getName(),
            $partnerUser->getRoles(),
            (string) $partnerUser->getPassword(),
            (int) $partner->getId(),
            (string) $partner->getName(),
        );
    }
}
