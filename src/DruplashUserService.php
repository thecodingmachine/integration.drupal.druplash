<?php


namespace Drupal\druplash;

use Drupal\user\Entity\User;
use Drupal\user\UserAuthInterface;
use Mouf\Security\UserService\AuthenticationListenerInterface;
use Mouf\Security\UserService\UserServiceInterface;

/**
 * User service integrating with Drupal management.
 */
class DruplashUserService implements UserServiceInterface
{
    /**
     * The user authentication.
     *
     * @var UserAuthInterface
     */
    private $userAuth;

    /**
     * This is an array containing all components that should be notified
     * when a user logs in or logs out.
     * All components in this array should implement the AuthenticationListenerInterface
     * interface.
     * For instance, the MoufRightsService, that manages the rights of users is
     * one of those.
     *
     * @var AuthenticationListenerInterface[]
     */
    private $authenticationListeners = [];

    /**
     * DruplashUserService constructor.
     * @param \Drupal\user\UserAuthInterface $userAuth
     * @param array $authenticationListeners
     */
    public function __construct(UserAuthInterface $userAuth, array $authenticationListeners)
    {
        $this->userAuth = $userAuth;
        $this->authenticationListeners = $authenticationListeners;
    }

    /**
     * @return $this
     */
    public function addAuthenticationListener(AuthenticationListenerInterface $authenticationListener)
    {
        $this->authenticationListeners[] = $authenticationListener;
        return $this;
    }

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
        $uid = $this->userAuth->authenticate($user, $password);

        if ($uid === false) {
            return false;
        }

        return $this->loginWithoutPassword($user);
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
            user_login_finalize($user);

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
        return \Drupal::currentUser()->isAuthenticated();
    }

    /**
     * Redirects the user to the login page if he is not logged.
     *
     * @return boolean
     */
    public function redirectNotLogged()
    {
        throw new \Exception('redirectNotLogged is deprecated and not used in Drupal 8');
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
        return \Drupal::currentUser()->id();
    }

    /**
     * Returns the current user login.
     *
     * @return string
     */
    public function getUserLogin()
    {
        if ($this->getUserId()) {
            return \Drupal::currentUser()->getAccountName();
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
            return new DruplashUser(\Drupal::currentUser());
        } else {
            return null;
        }
    }
}
