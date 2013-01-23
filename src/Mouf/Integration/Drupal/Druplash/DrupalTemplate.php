<?php
namespace Mouf\Integration\Drupal\Druplash;

use Mouf\Html\Utils\WebLibraryManager\WebLibraryManager;

use Mouf\Html\HtmlElement\HtmlBlock;
use Mouf\Html\Template\TemplateInterface;


/**
 * This class represents the template currently configured in Drupal.
 * It always comes with a "drupalTemplate" instance and you should call the "toHtml" method to trigger a rendering of the
 * template in Drupal.
 * 
 * @author David Négrier
 */
class DrupalTemplate implements TemplateInterface {
	
	protected $title;
	/**
	 * True if the toHtml method has been called, false otherwise.
	 * @var boolean
	 */
	protected $displayTriggered = false;
	
	/**
	 * The weblibrarymanager is in charge of handing JS files.
	 *
	 * @var WebLibraryManagerInterface
	 */
	private $webLibraryManager;
	
	/**
	 * 
	 * @var HtmlBlock
	 */
	private $contentBlock;
	    
	public function getContentBlock() 
	{
	  return $this->contentBlock;
	}
	
	/**
	 * The content of the page is represented by this object.
	 * Using this object, you can add content to your page.
	 * 
	 * @param HtmlBlock $value
	 */
	public function setContentBlock($value) 
	{
	  $this->contentBlock = $value;
	}
	
	/**
	 * Sets the title for the page.
	 * 
	 * @return TemplateInterface
	 */
	public function setTitle($title) {
		$this->title = $title;
		return $this;
	}
	
	public function getTitle() {
		return $this->title;
	}
	
	/**
	 * Returns the WebLibraryManager object that can be used to add JS/CSS files to this template.
	 *
	 * @return WebLibraryManager
	*/
	public function getWebLibraryManager() {
		return $this->webLibraryManager;
	}
	
	/**
	 * Sets the web library manager for this template.
	 *
	 * @Property
	 * @param WebLibraryManager $webLibraryManager
	 * @return BaseTemplate
	 */
	public function setWebLibraryManager(WebLibraryManager $webLibraryManager) {
		$this->webLibraryManager = $webLibraryManager;
		return $this;
	}
	
	/**
	 * Tells Drupal that the content should be rendered into the theme.
	 * This does actually not call any real rendering.
	 * It just sets a flag to inform Drupal that rendering should be performed (instead of going the Ajax way).
	 * 
	 * The toHtml() name is kept so that we can keep the same code between Splash and Druplash.
	 *
	 */
	public function toHtml() {
		$this->displayTriggered = true;
	}
	
	/**
	 * Returns true if the toHtml method has been called, false otherwise.
	 * 
	 * @return boolean
	 */
	public function isDisplayTriggered() {
		return $this->displayTriggered;
	}
}