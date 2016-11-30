<?php


namespace Mouf\Integration\Drupal\Druplash;

use Mouf\Security\UserService\AuthenticationListenerInterface;
use Mouf\Security\UserService\UserServiceInterface;

/**
 * User service integrating with Drupal management.
 */
class DruplashUserService implements UserServiceInterface
{

    /**
     * This is an array containing all components that should be notified
     * when a user logs in or logs out.
     * All components in this array should implement the AuthenticationListenerInterface
     * interface.
     * For instance, the MoufRightsService, that manages the rights of users is
     * one of those.
     *
     * @Property
     * @Compulsory
     * @var AuthenticationListenerInterface[]
     */
    public $authenticationListeners;

    /**
     * Logs the user using the provided login and password.
     * Returns true on success, false if the user or password is incorrect.
     *
     * @param string $user
     * @param string $password
     * @return boolean.
     */
    public function login($user, $password)
    {
        if($uid = user_authenticate($user, $password)) {
            $formState = array('uid' => $uid);
            user_login_submit(array(), $formState);

            if (is_array($this->authenticationListeners)) {
                foreach ($this->authenticationListeners as $listener) {
                    $listener->afterLogIn($this);
                }
            }

            return true;
        } else {
            return false;
        }
    }

    /**
     * Logs the user using the provided login.
     * The password is not needed if you use this function.
     * Of course, you should use this functions sparingly.
     * For instance, it can be useful if you want an administrator to "become" another
     * user without requiring the administrator to provide the password.
     *
     * @param string $login
     */
    public function loginWithoutPassword($login)
    {
        $user = user_load_by_name($login);
        if (empty($user)) {
            $user = user_load_by_mail($login);
        }

        if (empty($user)) {
            return false;
        } else {
            $formState = array('uid' => $user->uid);
            user_login_submit(array(), $formState);

            if (is_array($this->authenticationListeners)) {
                foreach ($this->authenticationListeners as $listener) {
                    $listener->afterLogIn($this);
                }
            }

            return true;
        }
    }

    /**
     * Logs a user using a token. The token should be discarded as soon as it
     * was used.
     *
     * @param string $token
     */
    public function loginViaToken($token)
    {
        throw new \Exception('Not implemented yet');
    }

    /**
     * Returns "true" if the user is logged, "false" otherwise.
     *
     * @return boolean
     */
    public function isLogged()
    {
        return user_is_logged_in();
    }

    /**
     * Redirects the user to the login page if he is not logged.
     *
     * @return boolean
     */
    public function redirectNotLogged()
    {
        drupal_access_denied();
    }

    /**
     * Logs the user off.
     *
     */
    public function logoff()
    {
        if (is_array($this->authenticationListeners)) {
            foreach ($this->authenticationListeners as $listener) {
                $listener->beforeLogOut($this);
            }
        }

        user_logout();
    }

    /**
     * Returns the current user ID.
     *
     * @return string
     */
    public function getUserId()
    {
        return $GLOBALS['user']->uid;
    }

    /**
     * Returns the current user login.
     *
     * @return string
     */
    public function getUserLogin()
    {
        if ($this->getUserId()) {
            return $GLOBALS['user']->name;
        } else {
            return null;
        }
    }

    /**
     * Returns the user that is logged (or null if no user is logged).
     *
     * return UserInterface
     */
    public function getLoggedUser()
    {
        if ($this->getUserId()) {
            return new DruplashUser($GLOBALS['user']);
        } else {
            return null;
        }
    }
}
