<?php

/* @var $object Mouf\Html\Utils\WebLibraryManager\InlineWebLibrary */
$cssElement = $object->getCssElement();
if ($cssElement) {
    ob_start();
    $cssElement->toHtml();
    $content = ob_get_clean();
    if (strpos($content, '<style') !== false) {
        $content = preg_replace('#<style(.*?)>(.*?)</style>$#is', '$2', $content);
    }
    drupal_add_css($content, 'inline');
}
