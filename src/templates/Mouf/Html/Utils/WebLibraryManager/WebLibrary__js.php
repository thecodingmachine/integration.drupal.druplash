<?php

$files = $object->getJsFiles();
if ($files) {
    foreach ($files as $file) {
        if (strpos($file, 'http://') === false && strpos($file, 'https://') === false && strpos($file, '/') !== 0) {
            drupal_add_js($file, 'file');
        } else {
            drupal_add_js($file, 'external');
        }
    }
}
