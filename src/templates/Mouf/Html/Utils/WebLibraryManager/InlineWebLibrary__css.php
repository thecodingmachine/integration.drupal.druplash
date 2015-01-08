<?php
/* @var $object Mouf\Html\Utils\WebLibraryManager\InlineWebLibrary */
$cssElement = $object->getCssElement();
if ($cssElement) {
	ob_start();
	$cssElement->toHtml();
	$content = ob_get_clean();
	
	drupal_add_css($content, 'inline');
}

