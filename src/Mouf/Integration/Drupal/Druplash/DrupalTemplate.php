<?php

namespace Mouf\Integration\Drupal\Druplash;

use Mouf\Html\HtmlElement\HtmlBlock;
use Mouf\Html\Template\BaseTemplate\BaseTemplate;

/**
 * This class represents the template currently configured in Drupal.
 * It always comes with a "drupalTemplate" instance and you should call the "toHtml" method to trigger a rendering of the
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

    public function getContentBlock()
    {
        return $this->content;
    }

    /**
     * The content of the page is represented by this object.
     * Using this object, you can add content to your page.
     *
     * @param HtmlBlock $value
     */
    public function setContentBlock($value)
    {
        $this->content = $value;
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
        // Let's register the template renderer in the default renderer.
        $this->getDefaultRenderer()->setTemplateRenderer($this->getTemplateRenderer());

        drupal_set_title($this->getTitle());
        $this->displayTriggered = true;
    }

    /**
     * Returns true if the toHtml method has been called, false otherwise.
     *
     * @return bool
     */
    public function isDisplayTriggered()
    {
        return $this->displayTriggered;
    }
}
