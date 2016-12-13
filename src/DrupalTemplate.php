<?php

namespace Drupal\druplash;

use Drupal\stratigility_bridge\DrupalArrayRenderCaller;
use Mouf\Html\HtmlElement\HtmlBlock;
use Mouf\Html\HtmlElement\HtmlElementInterface;
use Mouf\Html\Template\BaseTemplate\BaseTemplate;

/**
 * This class represents the template currently configured in Drupal.
 * Calling the "toHtml" method will trigger a rendering of the
 * template in Drupal.
 *
 * @author David Négrier
 */
class DrupalTemplate extends BaseTemplate
{
    /**
     * True if the toHtml method has been called, false otherwise.
     *
     * @var bool
     */
    protected $displayTriggered = false;

    /**
     * The object in charge of the rendering in Drupal.
     *
     * @var DrupalArrayRenderCaller
     */
    private $arrayRenderCaller;

    private $libraries = [];

    /**
     * @param DrupalArrayRenderCaller $arrayRenderCaller The object in charge of the rendering in Drupal.
     * @param HtmlElementInterface $content The content of the page.
     */
    public function __construct(DrupalArrayRenderCaller $arrayRenderCaller, HtmlElementInterface $content)
    {
        parent::__construct();
        $this->arrayRenderCaller = $arrayRenderCaller;
        $this->content = $content;
    }

    /**
     * Tells Drupal that the content should be rendered into the theme.
     * This does actually not call any real rendering.
     * It just sets a flag to inform Drupal that rendering should be performed (instead of going the Ajax way).
     *
     * The toHtml() name is kept so that we can keep the same code between Splash and Druplash.
     */
    public function toHtml()
    {
        ob_start();
        $this->content->toHtml();
        $content = ob_get_clean();

        $this->arrayRenderCaller->getResponse(array(
            '#theme' => 'druplash_renderer',
            '#title' => $this->getTitle(),
            '#content' => $content,
            '#cache' => ['max-age' => 0 ],
            '#attached' => [
                'library' => $this->libraries
            ]
        ));
        
        echo 'template';
    }

    public function getWebLibraryManager()
    {
        throw new \BadMethodCallException('Sorry, Druplash 8 does not support the WebLibraryManager concept due to restrictions in ways Drupal handles JS/CSS libraries. Use the "addLibrary" method instead.');
    }

    /**
     * Adds a Drupal library to the template (as defined in https://www.drupal.org/docs/8/creating-custom-modules/adding-stylesheets-css-and-javascript-js-to-a-drupal-8-module )
     *
     * @param string $library
     */
    public function addLibrary(string $library)
    {
        $this->libraries[] = $library;
    }
}
