<?php
/**
 * 
 * Right DAO using Drupal database.
 * @author Nicolas
 *
 * @Component
 */
class DruplashRightDao implements RightsDaoInterface {

	/**
	 * Returns a list of all the rights for the user passed in parameter.
	 *
	 * @param string $user_id
	 * @return array<RightInterface>
	 */
	public function getRightsForUser($user_id) {
		$account = user_load($user_id);
		$role_permissions = user_role_permissions($account->roles);
		$perms = array();
		foreach ($role_permissions as $one_role) {
			$perms += $one_role;
		}
		$rights = array();
		foreach ($perms as $perm => $value) {
			$rights[] = new MoufRight($perm);
		}
		return $rights;
	}

	/**
	 * Returns the RightInterface object associated to the user (or null if the
	 * user has no such right).
	 *
	 * @param string $user_id
	 * @param string $right
	 * @return RightInterface
	 */
	public function getRightForUser($user_id, $right) {
		if(user_access($right, user_load($user_id)))
			return new MoufRight($right);
		else
			return null;
	}
}