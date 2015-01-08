<?php
/* @var $object Mouf\Html\Utils\WebLibraryManager\InlineWebLibrary */
$jsElement = $object->getJsElement();
if ($jsElement) {
	ob_start();
	$jsElement->toHtml();
	$content = ob_get_clean();
	drupal_add_js($content, 'inline');
}

