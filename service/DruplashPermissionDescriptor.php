<?php
/**
 * 
 * Druplash permission descriptor: Schema of the permission given to the Drupal hook_permission.
 * @author Nicolas
 *
 * @Component
 */
class DruplashPermissionDescriptor {
	
	/**
	 * The name of the Drupal permission.
	 * 
	 * @Property
	 * @Compulsory
	 * @var string
	 */
	public $name;
	
	/**
	 * Title: The human-readable name of the permission, to be shown on the permission administration page.
	 * 
	 * @Property
	 * @Compulsory
	 * @var string
	 */
	public $title;
	
	/**
	 * Description: (optional) A description of what the permission does.
	 * 
	 * @Property
	 * @var string
	 */
	public $description;
	
	/**
	 * Restrict access: (optional) A boolean which can be set to TRUE to indicate that site administrators 
	 * should restrict access to this permission to trusted users. 
	 * This should be used for permissions that have inherent security risks across a variety of potential use cases 
	 * (for example, the "administer filters" and "bypass node access" permissions provided by Drupal core). 
	 * When set to TRUE, a standard warning message defined in user_admin_permissions() 
	 * will be associated with the permission and displayed with it on the permission administration page. 
	 * Defaults to FALSE. 
	 * 
	 * @Property
	 * @var bool
	 */
	public $restrictAccess = false;
}