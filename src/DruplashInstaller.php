<?php

/*
 * Copyright (c) 2013-2014 David Negrier
 *
 * See the file LICENSE.txt for copying permission.
 */

namespace Mouf\Integration\Drupal\Druplash;

use Mouf\Installer\PackageInstallerInterface;
use Mouf\MoufManager;
use Mouf\Actions\InstallUtils;
use Mouf\Html\Renderer\ChainableRendererInterface;

/**
 * An installer class for Druplash.
 */
class DruplashInstaller implements PackageInstallerInterface
{
    /**
     * (non-PHPdoc).
     *
     * @see \Mouf\Installer\PackageInstallerInterface::install()
     */
    public static function install(MoufManager $moufManager)
    {
        if ($moufManager->instanceExists('sessionManager')) {
            $moufManager->removeComponent('sessionManager');
        }
        $drupalSessionManager = $moufManager->createInstance('Mouf\\Integration\\Drupal\\Druplash\\DrupalSessionManager');
        $drupalSessionManager->setName('sessionManager');

        // Remove old deprecated defaultWebLibraryRenderer
        if ($moufManager->instanceExists('defaultWebLibraryRenderer')) {
            // Let's remove the default defaultWebLibraryRenderer :)
            $moufManager->removeComponent('defaultWebLibraryRenderer');
        }

        // Create the drupalTemplate instance
        $contentBlockDescriptor = InstallUtils::getOrCreateInstance('block.content', 'Mouf\\Html\\HtmlElement\\HtmlBlock', $moufManager);

        $drupalTemplateDescriptor = InstallUtils::getOrCreateInstance('drupalTemplate', 'Mouf\\Integration\\Drupal\\Druplash\\DrupalTemplate', $moufManager);
        if ($drupalTemplateDescriptor->getProperty('contentBlock')->getValue() == null) {
            $drupalTemplateDescriptor->getProperty('contentBlock')->setValue($contentBlockDescriptor);
        }

        $webLibraryManager = $moufManager->getInstanceDescriptor('defaultWebLibraryManager');
        if ($webLibraryManager && $drupalTemplateDescriptor->getProperty('webLibraryManager')->getValue() == null) {
            $drupalTemplateDescriptor->getProperty('webLibraryManager')->setValue($webLibraryManager);
        }

        // Let's delete and recreate the DrupalRightService.
        if ($moufManager->instanceExists('rightsService')) {
            $moufManager->removeComponent('rightsService');
        }

        $rightsService = $moufManager->createInstance('Mouf\\Integration\\Drupal\\Druplash\\DruplashRightService');
        $rightsService->setName('rightsService');
        if ($moufManager->instanceExists('errorLogLogger')) {
            $rightsService->getProperty('log')->setValue($moufManager->getInstanceDescriptor('errorLogLogger'));
        }

        $moufManager->declareComponent('userService', 'Mouf\\Integration\\Drupal\\Druplash\\DruplashUserService', false, MoufManager::DECLARE_ON_EXIST_KEEP_INCOMING_LINKS );
        $userService = $moufManager->getInstanceDescriptor('userService');

        $rightsService->getProperty('userService')->setValue($userService);

        $prevValues = $userService->getProperty('authenticationListeners')->getValue();
        $prevValues[] = $rightsService;
        $userService->getProperty('authenticationListeners')->setValue($prevValues);


        $druplashRenderer = InstallUtils::getOrCreateInstance('druplashRenderer', 'Mouf\\Html\\Renderer\\FileBasedRenderer', $moufManager);
        $druplashRenderer->getProperty('directory')->setValue('vendor/mouf/integration.drupal.druplash/src/templates');
        $druplashRenderer->getProperty('cacheService')->setValue($moufManager->getInstanceDescriptor('rendererCacheService'));
        $druplashRenderer->getProperty('type')->setValue(ChainableRendererInterface::TYPE_TEMPLATE);
        $druplashRenderer->getProperty('priority')->setValue(0);
        $drupalTemplateDescriptor->getProperty('templateRenderer')->setValue($druplashRenderer);
        $drupalTemplateDescriptor->getProperty('defaultRenderer')->setValue($moufManager->getInstanceDescriptor('defaultRenderer'));

        $druplash = InstallUtils::getOrCreateInstance('druplash', 'Mouf\\Integration\\Drupal\\Druplash\\Druplash', $moufManager);
        $moufExplorerUrlProvider = InstallUtils::getOrCreateInstance('moufExplorerUrlProvider', 'Mouf\\Mvc\\Splash\\Services\\MoufExplorerUrlProvider', $moufManager);

        if (!$druplash->getConstructorArgumentProperty('routeProviders')->isValueSet()) {
            $druplash->getConstructorArgumentProperty('routeProviders')->setValue(array(0 => $moufExplorerUrlProvider, ));
        }
        if (!$druplash->getConstructorArgumentProperty('drupalTemplate')->isValueSet()) {
            $druplash->getConstructorArgumentProperty('drupalTemplate')->setValue($drupalTemplateDescriptor);
        }


        // Let's rewrite the MoufComponents.php file to save the component
        $moufManager->rewriteMouf();
    }
}
