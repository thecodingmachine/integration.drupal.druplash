<?php
namespace Mouf\Integration\Drupal\Druplash;

use Mouf\Html\Utils\WebLibraryManager\WebLibraryInterface;

use Mouf\Html\Utils\WebLibraryManager\WebLibraryRendererInterface;

/**
 * The DrupalWebLibraryRenderer class is the Drupal way of adding JS ans CSS files.
 *  
 * @author David Négrier
 * @Component
 */
class DrupalWebLibraryRenderer implements WebLibraryRendererInterface {
	
	/**
	 * Renders the CSS part of a web library.
	 *
	 * @param WebLibrary $webLibrary
	 */
	public function toCssHtml(WebLibraryInterface $webLibrary) {
		$files = $webLibrary->getCssFiles();
		if ($files) {
			foreach ($files as $file) {
				if(strpos($file, 'http://') === false && strpos($file, 'https://') === false && strpos($file, '/') !== 0) {
					drupal_add_css($file, 'file');
				} else {
					drupal_add_css($file, 'external');
				}	
			}
		}
	}
	
	/**
	 * Renders the JS part of a web library.
	 *
	 * @param WebLibrary $webLibrary
	 */
	public function toJsHtml(WebLibraryInterface $webLibrary) {
		$files = $webLibrary->getJsFiles();
		if ($files) {
			foreach ($files as $file) {
				if(strpos($file, 'http://') === false && strpos($file, 'https://') === false && strpos($file, '/') !== 0) { 
					drupal_add_js($file);
				} else {
					drupal_add_js($file, 'inline');
				}
			}
		}
		
	}
	
	/**
	 * Renders any additional HTML that should be outputed below the JS and CSS part.
	 *
	 * @param WebLibrary $webLibrary
	 */
	public function toAdditionalHtml(WebLibraryInterface $webLibrary) {
		return "";
	}
}