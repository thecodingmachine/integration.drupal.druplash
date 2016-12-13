<?php

namespace Drupal\druplash;

use Drupal\user\Entity\User;
use Mouf\Integration\Drupal\Druplash\DruplashException;
use Mouf\Security\RightsService\RightsServiceInterface;

/**
 * Class to recover all Drupal application permissions.
 *
 * @author Nicolas
 */
class DruplashRightService implements RightsServiceInterface
{
    /**
     * Returns true if the current user has the right passed in parameter.
     * This method is overloaded for Drupal, because in Drupal, not authenticated users can have rights too.
     *
     * @param string $right
     * @param mixed  $scope
     *
     * @throws \InvalidArgumentException
     *
     * @return bool
     */
    public function isAllowed($right, $scope = null)
    {
        if ($scope !== null) {
            throw new \InvalidArgumentException('The DruplashRightService does not support scopes');
        }

        return \Drupal::currentUser()->hasPermission($right);
    }

    /**
     * Returns true if the user whose id is $user_id has the $right.
     * A scope can be optionnally passed.
     * A scope can be anything from a string to an object. If it is an object,
     * it must be serializable (because it will be stored in the session).
     *
     * @param string $user_id
     * @param string $right
     * @param mixed  $scope
     *
     * @throws \InvalidArgumentException
     *
     * @return bool
     */
    public function isUserAllowed($user_id, $right, $scope = null)
    {
        if ($scope !== null) {
            throw new \InvalidArgumentException('The DruplashRightService does not support scopes');
        }

        return User::load($user_id)->hasPermission($right);
    }

    /**
     * Rights are cached in session, this function will purge the rights in session.
     * This can be useful if you know the rights previously fetched for
     * the current user will change.
     */
    public function flushRightsCache()
    {
        // Let's do nothing, this is managed by Drupal anyway.
    }

    /**
     * If the user has not the requested right, this function will
     * redirect the user to an error page (or a login page...).
     *
     * @param string $right
     * @param mixed  $scope
     */
    public function redirectNotAuthorized($right, $scope = null)
    {
        throw new DruplashException('This method is not supported in Druplash');
    }
}
