<?php


namespace Mouf\Integration\Drupal\Druplash;


use Mouf\Security\UserService\UserInterface;

class DruplashUser implements UserInterface
{
    private $drupalUser;

    /**
     * @param $drupalUser
     */
    public function __construct($drupalUser)
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
        return $this->drupalUser->uid;
    }

    /**
     * Returns the login for the current user.
     *
     * @return string
     */
    public function getLogin()
    {
        return $this->drupalUser->name;
    }
}
