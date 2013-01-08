<?php

/**
 * The Drupal Dynamic Block Interface is a Mouf interface that represents a block in Drupal.
 * When you create a Mouf instance of a class that implements the DrupalDynamicBlockInterface, this instance will appear automatically in Drupal blocks.
 * 
 * You can use the DrupalDynamicBlock class that implements the interface in a "usual" way.
 * 
 * @author David
 */
interface DrupalDynamicBlockInterface {
	
	/**
	 * Returns the block name, as displayed in Drupal's interface.
	 * 
	 * @return string
	 */
	public function getName();
	
	/**
	 * Returns the caching strategy for this block.
	 * A bitmask of flags describing how the block should behave with respect to block caching. The following shortcut bitmasks are provided as constants in block.module:
     * 	BLOCK_CACHE_PER_ROLE (default): The block can change depending on the roles the user viewing the page belongs to.
     * 	BLOCK_CACHE_PER_USER: The block can change depending on the user viewing the page. This setting can be resource-consuming for sites with large number of users, and should only be used when BLOCK_CACHE_PER_ROLE is not sufficient.
     * 	BLOCK_CACHE_PER_PAGE: The block can change depending on the page being viewed.
     * 	BLOCK_CACHE_GLOBAL: The block is the same for every user on every page where it is visible.
     * 	BLOCK_NO_CACHE: The block should not get cached.
	 * 
	 * @return int
	 */
	public function getCache();
	
	/**
	 * Returns the default weight of the block (only used on block initialization in Drupal and can be overloaded in Drupal's admin view).
	 * 
	 * @return int
	 */
	public function getWeight();

	/**
	 * Returns the default status of the block (only used on block initialization in Drupal and can be overloaded in Drupal's admin view).
	 * 
	 * @return string
	 */
	public function getStatus();
	
	/**
	 * Returns the default region of the block (only used on block initialization in Drupal and can be overloaded in Drupal's admin view).
	 * Note that if you set a region that isn't available in a given theme, the block will be registered instead to that theme's default region (the first item in the Drupal's _regions array). 
	 * 
	 * @return string
	 */
	public function getRegion();
	
	
	/**
	 * Returns the default visibility of the block (only used on block initialization in Drupal and can be overloaded in Drupal's admin view).
	 * 
	 * @return string
	 */
	public function getVisibility();
	
	/**
	 * Returns the default pages a block can be seend on (only used on block initialization in Drupal and can be overloaded in Drupal's admin view).
	 * 
	 * @return string
	 */
	public function getPages();
	
	/**
	 * Returns the title of the block.
	 * 
	 * @return string
	 */
	public function getSubject();
	
	/**
	 * Returns the body of the block.
	 * 
	 * @var string
	 */
	public function getContent();
}

?>