<?php

/**
 * The Drupal Dynamic Block is a Mouf component that represent a block in Drupal.
 * When you create an instance of the DrupalDynamicBlock, it directly appears in Drupal, as a block.
 * 
 * The interesting part is you can change dynamically the text of the block via the setBody() method.
 * 
 * @author David
 * @Component
 */
class DrupalDynamicBlock implements DrupalDynamicBlockInterface, Scopable {
	
	/**
	 * The block name, as displayed in Drupal's interface.
	 * 
	 * @Property
	 * @Compulsory
	 * @var string
	 */
	public $name;
	
	/**
	 * The caching strategy for this block.
	 * A bitmask of flags describing how the block should behave with respect to block caching. The following shortcut bitmasks are provided as constants in block.module:
     * 	DRUPAL_CACHE_PER_ROLE (default): The block can change depending on the roles the user viewing the page belongs to.
     * 	DRUPAL_CACHE_PER_USER: The block can change depending on the user viewing the page. This setting can be resource-consuming for sites with large number of users, and should only be used when BLOCK_CACHE_PER_ROLE is not sufficient.
     * 	DRUPAL_CACHE_PER_PAGE: The block can change depending on the page being viewed.
     * 	DRUPAL_CACHE_GLOBAL: The block is the same for every user on every page where it is visible.
     * 	DRUPAL_NO_CACHE: The block should not get cached.
	 * 
	 * @Property
	 * @var int
	 */
	public $cache;
	
	/**
	 * The default weight of the block (only used on block initialization in Drupal and can be overloaded in Drupal's admin view).
	 * 
	 * @Property
	 * @var int
	 */
	public $weight;

	/**
	 * The default status of the block (only used on block initialization in Drupal and can be overloaded in Drupal's admin view).
	 * 
	 * @Property
	 * @var string
	 */
	public $status;
	
	/**
	 * The default region of the block (only used on block initialization in Drupal and can be overloaded in Drupal's admin view).
	 * Note that if you set a region that isn't available in a given theme, the block will be registered instead to that theme's default region (the first item in the Drupal's _regions array). 
	 * 
	 * @Property
	 * @var string
	 */
	public $region;
	
	
	/**
	 * The default visibility of the block (only used on block initialization in Drupal and can be overloaded in Drupal's admin view).
	 * 
	 * @Property
	 * @var string
	 */
	public $visibility;
	
	/**
	 * The default pages a block can be seend on (only used on block initialization in Drupal and can be overloaded in Drupal's admin view).
	 * 
	 * @Property
	 * @var string
	 */
	public $pages;
	
	/**
	 * The title of the block.
	 * 
	 * @Property
	 * @var string
	 */
	public $subject;
	
	/**
	 * The body of the block.
	 * 
	 * @Property
	 * @var array<HtmlElementInterface>
	 */
	public $content;
	
	/**
	 * Returns the block name, as displayed in Drupal's interface.
	 * 
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}
	
	/**
	 * Returns the caching strategy for this block.
	 * A bitmask of flags describing how the block should behave with respect to block caching. The following shortcut bitmasks are provided as constants in block.module:
     * 	DRUPAL_CACHE_PER_ROLE (default): The block can change depending on the roles the user viewing the page belongs to.
     * 	DRUPAL_CACHE_PER_USER: The block can change depending on the user viewing the page. This setting can be resource-consuming for sites with large number of users, and should only be used when BLOCK_CACHE_PER_ROLE is not sufficient.
     * 	DRUPAL_CACHE_PER_PAGE: The block can change depending on the page being viewed.
     * 	DRUPAL_CACHE_GLOBAL: The block is the same for every user on every page where it is visible.
     * 	DRUPAL_NO_CACHE: The block should not get cached.
	 * 
	 * @return int
	 */
	public function getCache() {
		if (is_string($this->cache) && defined($this->cache)) {
			return constant($this->cache);
		}
		
		return $this->cache;
	}
	
	/**
	 * Returns the default weight of the block (only used on block initialization in Drupal and can be overloaded in Drupal's admin view).
	 * 
	 * @return int
	 */
	public function getWeight() {
		return $this->weight;
	}

	/**
	 * Returns the default status of the block (only used on block initialization in Drupal and can be overloaded in Drupal's admin view).
	 * 
	 * @return string
	 */
	public function getStatus() {
		return $this->status;
	}
	
	/**
	 * Returns the default region of the block (only used on block initialization in Drupal and can be overloaded in Drupal's admin view).
	 * Note that if you set a region that isn't available in a given theme, the block will be registered instead to that theme's default region (the first item in the Drupal's _regions array). 
	 * 
	 * @return string
	 */
	public function getRegion() {
		return $this->region;
	}
	
	
	/**
	 * Returns the default visibility of the block (only used on block initialization in Drupal and can be overloaded in Drupal's admin view).
	 * 
	 * @return string
	 */
	public function getVisibility() {
		return $this->visibility;
	}
	
	/**
	 * Returns the default pages a block can be seend on (only used on block initialization in Drupal and can be overloaded in Drupal's admin view).
	 * 
	 * @return string
	 */
	public function getPages() {
		return $this->pages;
	}
	
	/**
	 * Returns the title of the block.
	 * 
	 * @return string
	 */
	public function getSubject() {
		return $this->subject;
	}
	
	/**
	 * Returns the body of the block.
	 * 
	 * @var string
	 */
	public function getContent() {
		ob_start();
		if (!empty($this->content)) {
			foreach ($this->content as $element) {
				$element->toHtml();
			}
		}
		return ob_get_clean();
	}
	
/**
	 * Adds some content to the main panel by calling the function passed in parameter.
	 * @return SplashTemplate
	 */
	public function addContentFunction($function) {
		$arguments = func_get_args();
		// Remove the first argument
		array_shift($arguments);

		$content = new HtmlFromFunction();
		$content->functionPointer = $function;
		$content->parameters = $arguments;
		$this->content[] = $content;
		return $this;
	}

	/**
	 * Adds some content to the main panel by displaying the text passed in parameter.
	 * @return SplashTemplate
	 */
	public function addContentText($text) {
		$content = new HtmlString();
		$content->htmlString = $text;
		$this->content[] = $content;
		return $this;
	}
	
	/**
	 * Adds some content to the main panel by displaying the text in the file passed in parameter.
	 * The scope is the object that will refer the $this.
	 * @return SplashTemplate
	 */
	public function addContentFile($fileName, Scopable $scope = null) {
		$content = new HtmlFromFile();
		$content->fileName = $fileName;
		if ($scope != null) {
			$content->scope = $scope;
		} else {
			$content->scope = $this;
		}
		$this->content[] = $content;
		
		return $this;
	}
	
	/**
	 * Adds an object extending the HtmlElementInterface interface to the content of the page.
	 *
	 * @param HtmlElementInterface $element
	 * @return SplashTemplate
	 */
	public function addContentHtmlElement(HtmlElementInterface $element) {
		$this->content[] = $element;
		return $this;
	}
	
	/**
	 * Inludes the file (useful to load a view inside the Controllers scope).
	 *
	 * @param unknown_type $file
	 */
	public function loadFile($file) {
		include $file;
	}
}

?>