<?php

namespace App\Service;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Service to manage users
 *
 * @author Dany Pignoux <dany.pignoux@outlook.fr>
 */
class UserService
{
    /**
     * Check if the specified user has the specified role
     *
     * @param UserInterface|null $user The user
     * @param string $category The article-category
     * @param string $role The expected role
     *
     * @return bool TRUE if the specified user has the specified role, FALSE otherwise
     */
    public function hasRole(?UserInterface $user, string $category, string $role): bool
    {
        return $user && in_array(strtoupper('ROLE_' . $category . '_' . $role), $user->getRoles());
    }

    /**
     * Check if the two specified users are equals
     *
     * @param UserInterface|null $a
     * @param UserInterface|null $b
     *
     * @return bool TRUE if the two specified users are equals, FALSE otherwise (also if the two users are null)
     */
    public function equals(?UserInterface $a, ?UserInterface $b): bool
    {
        return ($a && $b && ($a->getUserIdentifier() === $b->getUserIdentifier()));
    }
}