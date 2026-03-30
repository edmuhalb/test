<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\User\User;
use App\Repository\UserRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

final class UserProvider implements UserProviderInterface
{
    private UserRepository $users;

    public function __construct(
        UserRepository $users
    ) {
        $this->users = $users;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        if (!$user = $this->loadUserByUsername($identifier)) {
            throw new UserNotFoundException('');
        }

        return self::identityByUser($user);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function refreshUser(UserInterface $user): UserIdentity
    {
        if (!$user instanceof UserIdentity) {
            throw new UnsupportedUserException(\sprintf('Invalid user class "%s".', $user::class));
        }

        if (!$userModel = $this->loadUserByUsername($user->getUserIdentifier())) {
            throw new UserNotFoundException('');
        }

        return self::identityByUser($userModel);
    }

    public function supportsClass(string $class): bool
    {
        return UserIdentity::class === $class || is_subclass_of($class, UserIdentity::class);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function loadUserByUsername(string $username): ?User
    {
        return $this->users->findOneByPhone($username);
    }

    private static function identityByUser(User $user): UserIdentity
    {
        /** @var int $userID */
        $userID =  $user->getId();
        /** @var string $passwordHash */
        $passwordHash = $user->getPassword();
        /** @var string $userFullName */
        $userFullName = $user->getName();

        return new UserIdentity(
            $userID,
            $user->getPhone(),
            $user->getName(),
            $user->getPermissions(),
            $passwordHash,
            $user->getAvatar(),
            $user->getPosition(),
            $user->isActive() ? 1 : 0,
        );
    }
}
