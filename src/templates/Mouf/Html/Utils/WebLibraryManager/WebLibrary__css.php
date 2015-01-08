<?php
$files = $object->getCssFiles();
if ($files) {
	foreach ($files as $file) {
		if(strpos($file, 'http://') === false && strpos($file, 'https://') === false && strpos($file, '/') !== 0) {
			drupal_add_css($file, 'file');
		} else {
			drupal_add_css($file, 'external');
		}
	}
}