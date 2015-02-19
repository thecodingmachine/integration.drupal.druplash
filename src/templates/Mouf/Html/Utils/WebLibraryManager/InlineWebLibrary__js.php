<?php
/* @var $object Mouf\Html\Utils\WebLibraryManager\InlineWebLibrary */
$jsElement = $object->getJsElement();
if ($jsElement) {
	ob_start();
	$jsElement->toHtml();
	$content = ob_get_clean();
	if(strpos($content, "<script") !== false) {
		$content = preg_replace('#<script(.*?)>(.*?)</script>$#is', '$2', $content);
	}
	drupal_add_js($content, 'inline');
}

