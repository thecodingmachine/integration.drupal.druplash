<?php


namespace Drupal\druplash;

use Interop\Container\ContainerInterface;
use Interop\Container\ServiceProvider;
use Mouf\Mvc\Splash\Routers\SplashDefaultRouter;

class DruplashServiceProvider implements ServiceProvider
{

    /**
     * Returns a list of all container entries registered by this service provider.
     *
     * - the key is the entry name
     * - the value is a callable that will return the entry, aka the **factory**
     *
     * Factories have the following signature:
     *        function(ContainerInterface $container, callable $getPrevious = null)
     *
     * About factories parameters:
     *
     * - the container (instance of `Interop\Container\ContainerInterface`)
     * - a callable that returns the previous entry if overriding a previous entry, or `null` if not
     *
     * @return callable[]
     */
    public function getServices()
    {
        return [
            'stratigility_pipe' => [ self::class, 'registerSplashInStratigilityPipe' ]
        ];
    }

    public static function registerSplashInStratigilityPipe(ContainerInterface $container, callable $previous = null)
    {
        $stratigilityPipe = $previous();

        $stratigilityPipe->pipe($container->get(SplashDefaultRouter::class));
        return $stratigilityPipe;
    }
}