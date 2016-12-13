<?php

namespace Drupal\druplash;

use Doctrine\Common\Annotations\AnnotationException;
use Mouf\Mvc\Splash\Services\ControllerAnalyzer;
use Mouf\Mvc\Splash\Services\ControllerDetector;

/**
 * This class scans the Mouf container in order to find all instances that point to classes containing a @URL or @Action annotation.
 * Use it to discover instances.
 */
class DruplashControllerExplorer implements ControllerDetector
{
    /**
     * Returns a list of controllers.
     * It is the name of the controller (in the container) that is returned (not the container itself).
     *
     * @param ControllerAnalyzer $controllerAnalyzer
     *
     * @return array|\string[]
     *
     * @throws \Drupal\Core\DependencyInjection\ContainerNotInitializedException
     * @throws AnnotationException
     */
    public function getControllerIdentifiers(ControllerAnalyzer $controllerAnalyzer) : array
    {
        $container = \Drupal::getContainer();
        $instanceNames = $container->getServiceIds();

        $isController = [];

        $controllers = [];

        /** @var string[] $instanceNames */
        foreach ($instanceNames as $instanceName) {
            try {
                $instance = $container->get($instanceName);
            } catch (\Exception $e) {
                // Let's ignore any service failing to be created.
                continue;
            }
            if (!is_object($instance)) {
                continue;
            }
            $className = get_class($instance);

            if (!isset($isController[$className])) {
                try {
                    $isController[$className] = $controllerAnalyzer->isController($className);
                } catch (AnnotationException $e) {
                    // Unknown annotation?
                    // Is there a slight chance this class might be a controller? Let's apply heuristics here.
                    if ($this->shouldBeController($className)) {
                        throw $e;
                    }

                    // Let's bypass the controller altogether.
                    $isController[$className] = false;
                }
            }

            if ($isController[$className] === true) {
                $controllers[] = $instanceName;
            }
        }

        return $controllers;
    }

    /**
     * If we arrive in this method, annotations have failed to parse.
     * Let's try to see (heuristically) if this class has a good chance to be a controller or not.
     * If it has, let's display a big error message.
     *
     * @param string $className
     *
     * @return bool
     */
    private function shouldBeController($className) : bool
    {
        if (strpos($className, 'Controller') !== false && strpos($className, 'Drupal\\Core') === false) {
            return true;
        }

        $reflectionClass = new \ReflectionClass($className);
        $file = $reflectionClass->getFileName();

        $content = file_get_contents($file);

        if (strpos($content, '@URL') !== false) {
            return true;
        }

        return false;
    }

    /**
     * Returns a unique tag representing the list of SplashRoutes returned.
     * If the tag changes, the cache is flushed by Splash.
     *
     * Important! This must be quick to compute.
     *
     * @return mixed
     */
    public function getExpirationTag() : string
    {
        // TODO: maybe store a random value in the Drupal cache?
        // When Drupal cache is purged, our cache is purged too?
        // Or better, listen to the cache purge event to purge the Stash cache.
        return 'TODO_FIND_A_GOOD_KEY';
    }
}
