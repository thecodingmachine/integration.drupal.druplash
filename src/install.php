<?php
use Mouf\MoufManager;
use Mouf\Actions\InstallUtils;

// First, let's request the install utilities
require_once __DIR__."/../../../autoload.php";

// Let's init Mouf
InstallUtils::init(InstallUtils::$INIT_APP);

// Let's create the instance
$moufManager = MoufManager::getMoufManager();
if ($moufManager->instanceExists("sessionManager")) {
	$moufManager->removeComponent("sessionManager");
}
$drupalSessionManager = $moufManager->createInstance("Mouf\\Integration\\Drupal\\Druplash\\DrupalSessionManager");
$drupalSessionManager->setName("sessionManager");


// Provide a defaultWebLibraryRenderer adapted to Drupal
if ($moufManager->instanceExists("defaultWebLibraryRenderer")) {
	// Let's remove the default defaultWebLibraryRenderer :)
	$moufManager->removeComponent("defaultWebLibraryRenderer");
}
$drupalWebLibraryManager = $moufManager->createInstance("Mouf\\Integration\\Drupal\\Druplash\\DrupalWebLibraryRenderer");
$drupalWebLibraryManager->setName("defaultWebLibraryRenderer");

// Create the drupalTemplate instance
$contentBlockDescriptor = InstallUtils::getOrCreateInstance("block.content", "Mouf\\Html\\HtmlElement\\HtmlBlock", $moufManager);

$drupalTemplateDescriptor = InstallUtils::getOrCreateInstance("drupalTemplate", "Mouf\\Integration\\Drupal\\Druplash\\DrupalTemplate", $moufManager);
if ($drupalTemplateDescriptor->getProperty("contentBlock") == null) {
	$drupalTemplateDescriptor->getProperty("contentBlock")->setValue($contentBlockDescriptor);
}

$webLibraryManager = $moufManager->getInstanceDescriptor('defaultWebLibraryManager');
if ($webLibraryManager && $template->getProperty('webLibraryManager')->getValue() == null) {
	$template->getProperty('webLibraryManager')->setValue($webLibraryManager);
}

// Let's rewrite the MoufComponents.php file to save the component
$moufManager->rewriteMouf();

// Finally, let's continue the install
InstallUtils::continueInstall();
?>