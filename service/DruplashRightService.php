<?php
/**
 * 
 * Class to recover all Drupal application permissions.
 * @author Nicolas
 *
 * @Component
 */
class DruplashRightService extends MoufRightService {
	
	/**
	 * List of specific permissions for the application.
	 * 
	 * @Property
	 * @var array<DruplashPermissionDescriptor>
	 */
	public $drupalPermissions = array();
	
	/**
	 * Get all Drupal permissions for the application as an array understood by hook_permission.
	 * 
	 * @return array
	 */
	public function getDrupalPermissions() {
		$allPermissions = array();
		foreach ($this->drupalPermissions as $drupalPermission) {
			/* @var $drupalPermission DrupalPermissionDescriptor */
			$allPermissions[$drupalPermission->name]['title'] = t($drupalPermission->title);
			if($drupalPermission->description)
				$allPermissions[$drupalPermission->name]['description'] = t($drupalPermission->description);
			if($drupalPermission->restrictAccess)
				$allPermissions[$drupalPermission->name]['restrict access'] = $drupalPermission->restrictAccess;
		}
		return $allPermissions;
	}
	
	/**
	 * Returns true if the current user has the right passed in parameter.
	 * This method is overloaded for Drupal, because in Drupal, not authenticated users can have rights too. 
	 *
	 * @param string $right
	 * @param mixed $scope
	 */
	public function isAllowed($right, $scope = null) {
		if ($scope != null) {
			throw new MoufException("The DruplashRightService does not support scopes");
		}
		return user_access($right);
	}
}