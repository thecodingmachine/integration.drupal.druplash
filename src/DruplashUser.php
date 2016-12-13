<?php


namespace Drupal\druplash;


use Drupal\user\Entity\User;
use Mouf\Security\UserService\UserInterface;

class DruplashUser implements UserInterface
{
    private $drupalUser;

    /**
     * @param $drupalUser
     */
    public function __construct(User $drupalUser)
    {
        $this->drupalUser = $drupalUser;
    }


    /**
     * Returns the ID for the current user.
     *
     * @return string
     */
    public function getId()
    {
        return $this->drupalUser->id();
    }

    /**
     * Returns the login for the current user.
     *
     * @return string
     */
    public function getLogin()
    {
        return $this->drupalUser->getUsername();
    }
}
