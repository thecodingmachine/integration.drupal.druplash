<?php

namespace Drupal\druplash;

use TheCodingMachine\Interop\ServiceProviderBridgeBundle\SymfonyContainerAdapter;

/**
 * Drupal container only contains lower case services!
 * So we need to lower case services before tapping in the container.
 */
class DrupalContainerAdapter extends SymfonyContainerAdapter
{
    public function get($id)
    {
        if (parent::has($id)) {
            return parent::get($id);
        }

        return parent::get(strtolower($id));
    }

    public function has($id)
    {
        return parent::has($id) || parent::has(strtolower($id));
    }
}
