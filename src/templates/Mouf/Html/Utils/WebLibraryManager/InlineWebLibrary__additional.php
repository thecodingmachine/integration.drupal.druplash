<?php

/* @var $object Mouf\Html\Utils\WebLibraryManager\InlineWebLibrary */
$additionalElement = $object->getAdditionalElement();
if ($additionalElement) {
	ob_start();
	$additionalElement->toHtml();
	$content = ob_get_clean();
	
	// FIXME
	drupal_add_js($content, 'inline');
}

