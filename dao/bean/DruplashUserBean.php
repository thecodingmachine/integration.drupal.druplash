<?php
/**
 * 
 * User bean for Drupal user.
 * @author Nicolas
 *
 */
class DruplashUserBean implements UserInterface {
	
	/**
	 * Drupal user bean.
	 * 
	 * @var stdClass
	 */
	private $user;
	
	/**
	 * Load the Drupal user bean.
	 * 
	 * @param int $uid
	 */
	public function __construct($uid) {
		$this->user = user_load($uid);
	}
	
	/**
	 * Returns the ID for the current Drupal user.
	 *
	 * @return int
	 */
	public function getId() {
		return $this->user->uid;
	}
	
	/**
	 * Returns the login for the current Drupal user.
	 *
	 * @return string
	 */
	public function getLogin() {
		return $this->user->name;
	}
}