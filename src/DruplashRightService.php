<?php

namespace Mouf\Integration\Drupal\Druplash;

use Mouf\Security\RightsService\MoufRightService;
use Mouf\MoufException;

/**
 * Class to recover all Drupal application permissions.
 *
 * @author Nicolas
 */
class DruplashRightService extends MoufRightService
{
    /**
     * Returns true if the current user has the right passed in parameter.
     * This method is overloaded for Drupal, because in Drupal, not authenticated users can have rights too.
     *
     * @param string $right
     * @param mixed  $scope
     */
    public function isAllowed($right, $scope = null)
    {
        if ($scope != null) {
            throw new MoufException('The DruplashRightService does not support scopes');
        }

        return \Drupal::currentUser()->hasPermission($right);
    }
}
