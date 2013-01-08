<?php
/**
 * 
 * User DAO using Drupal database.
 * @author Nicolas
 *
 * @Component
 */
class DruplashUserDao implements UserDaoInterface {
	
	/**
	 * Drupal users.
	 * 
	 * @var array<int, DruplashUserBean>
	 */
	private $users;
	
	/**
	 * Returns a user from its login and its password, or null if the login or credentials are false.
	 *
	 * @param string $login
	 * @param string $password
	 * @return UserInterface
	 */
	public function getUserByCredentials($login, $password) {
		$id = user_authenticate($login, $password);
		return $this->getUserById($id);
	}

	/**
	 * Returns a user from its token.
	 *
	 * @param string $token
	 * @return UserInterface
	 */
	public function getUserByToken($token) {
		throw new Exception('Not implemented');
	}
	
	/**
	 * Discards a token.
	 *
	 * @param string $token
	 */
	public function discardToken($token) {
		throw new Exception('Not implemented');
	}
	
	/**
	 * Returns a user from its ID
	 *
	 * @param string $id
	 * @return UserInterface
	 */
	public function getUserById($id) {
		if(!isset($this->users[$id]))
			$this->users[$id] = new DruplashUserBean($id);
		return $this->users[$id];
	}
	
	/**
	 * Returns a user from its login
	 *
	 * @param string $login
	 * @return UserInterface
	 */
	public function getUserByLogin($login) {
		$id = user_load_by_name($login);
		return $this->getUserById($id);
	}
}