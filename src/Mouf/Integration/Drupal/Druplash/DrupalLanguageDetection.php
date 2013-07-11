<?php 
namespace Mouf\Integration\Drupal\Druplash;

use Mouf\Utils\I18n\Fine\Language\LanguageDetectionInterface;

/**
 * The DrupalLanguageDetection class returns the language detected by Drupal.
 * Of course, Drupal must be installed, and bootstraped.
 * The easiest way to do this is to use the Druplash module.
 * 
 * @author David Negrier
 */
class DrupalLanguageDetection implements LanguageDetectionInterface {
	
	/**
	 * While FINE as a notion if "default" language, Drupal as not.
	 * Instead, the default language for Drupal is always "English".
	 * By ticking this box, if Drupal returns "english", this class
	 * will return "default" instead.
	 * 
	 * @var boolean
	 */
	public $englishAsDefault;
	
	/**
	 * (non-PHPdoc)
	 * @see \Mouf\Utils\I18n\Fine\Language\LanguageDetectionInterface::getLanguage()
	 */
	public function getLanguage() {
		global $language;
		if (!isset($language) || !isset($language->language)) {
			return "default";
		}
		$lang = $language->language;
		if ($this->englishAsDefault && $lang=="en") {
			$lang = "default";
		}
		return $lang;
	}

}