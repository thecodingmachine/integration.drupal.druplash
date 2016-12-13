Druplash tutorial: how to build an application based on Drupal, using a MVC framework
=====================================================================================

Druplash is a MVC framework based on SplashMVC, that is directly integrated with Drupal. As a result, you can directly use an Object Oriented paradigm, with full MVC support inside your Drupal website.

Creating a controller
---------------------

The first thing you will want to do in Druplash is to create your controller, to display a web page.
If you are not familiar with Splash controllers, start by [reading this page about Splash controllers](https://github.com/thecodingmachine/mvc.splash-common/blob/8.0/doc/writing_controllers_manually.md).

Creating a controller in Druplash is similar to creating a controller in Splash:

- Step 1: Create the controller class, with the action
- Step 2: Create an instance of the class in the Drupal container (you can use *.services.yml files or container-interop/service-providers) for this
- Step 3: Clear Drupal's cache (only Step 3 is different, since it is Drupal's own cache that you must purge)

The controller class
--------------------

Here is a sample controller class you might use:

```php
<?php  
namespace Test\Controllers;

use Mouf\Mvc\Splash\Annotations\URL;
use Mouf\Html\Template\TemplateInterface;
use Mouf\Html\HtmlElement\HtmlBlock;
use \Twig_Environment;
use Mouf\Html\Renderer\Twig\TwigTemplate;
use Mouf\Mvc\Splash\HtmlResponse;
use Zend\Diactoros\Response\JsonResponse;

class MyController {

    /**
     * The template used by this controller.
     * @var TemplateInterface
     */
    private $template;

    /**
     * The main content block of the page.
     * @var HtmlBlock
     */
    private $content;

    /**
     * The Twig environment (used to render Twig templates).
     * @var Twig_Environment
     */
    private $twig;

    /**
     * Controller's constructor.
     * @param TemplateInterface $template The template used by this controller
     * @param HtmlBlock $content The main content block of the page
     * @param Twig_Environment $twig The Twig environment (used to render Twig templates)
     */
    public function __construct(TemplateInterface $template, HtmlBlock $content, Twig_Environment $twig) {
        $this->template = $template;
        $this->content = $content;
        $this->twig = $twig;
    }

    /**
     * This method will be called when we access the /helloworld URL.*
     * It accepts an optional "echo" parameter.
     * 
     * @URL("/helloworld")
     */
    public function helloworld($echo = '') {
        // Typical code to access database goes here.

        // We declare the view, and bind it to the "content" block.
        // The view is declared in a Twig file.
        // Let's add the twig file to the template.
        $this->content->addHtmlElement(new TwigTemplate($this->twig, 'views/myview.twig', ["echo"=>$echo]));

        // Finally, we draw the template.
        return new HtmlResponse($this->template);
    }

    /**
     * Just echoing some text will output the text directly.
     * This is useful for Ajax calls.
     * 
     * @URL("/helloworld_ajax")
     */
    public function helloworld2() {
        return new JsonResponse(array('hello'=>'world'));
    }
}
```

If you have been using Splash, you will notice that the controller's code is exactly the same as the code from Splash.
This means that you can use the same controller's code in Splash and in Druplash. Therefore, this means you can
migrate a Splash application in Druplash without changing your code!

... yes, we know it's great :)

Of course, we need the "view" file associated with this controller.

**views/myview.twig**
```twig
<h1>Hello world!</h1>
<p>echo: {{ echo }}</p>
```

Note: Drupal looks for Twig files from the web root of Drupal. So if your web-root is the "web" directory, you should put your Twig file in `web/views/myview.twig`.

Step 2: create an instance of the controller
--------------------------------------------

So far, we have created a class, but a class is useless if Splash cannot get an instance of it from the container.

Druplash will analyze all services in the Drupal container and find controllers in the container by itself. All you have to do is to declare your controller in the Drupal container. 

To do so, you have 2 options.

### Option 1: the Drupal way

Create a module, and in your module, add a *.services.yml file:

**my_module.services.yml**
```yml
services:
  test_controller:
    class: Test\Controllers\MyController
```

### Option 2: the container-interop way

Alternatively, you can create a container-interop service provider and declare your controller in this service provider.
Container-interop ServiceProviders are portable and can be used across many frameworks so your code stays portable.
 
Create a service-provider:

```php
<?php
namespace Test\DI;

use Interop\Container\ContainerInterface;
use Interop\Container\ServiceProvider;
use Mouf\Html\Template\TemplateInterface;
use Test\Controllers\TestController;

class TestServiceProvider implements ServiceProvider
{
    public function getServices()
    {
        return [
            // This factory creates the controller
            TestController::class => function(ContainerInterface $container) {
                return new TestController($container->get(TemplateInterface::class), $container->get('content.block'), , $container->get('twig'));
            }
        ];
    }
}
```

Declare the service provider so that Drupal can find it:

- create a `service-providers.php` file at the web root
- in this file, add the following content:

```php
<?php
return [
    'service-providers' => [
        \Test\DI\TestServiceProvider::class
    ],
    'puli' => false
];
```

Step 3: clear Drupal's cache and test
-------------------------------------

Now, our instance is created. All that remains to do is to clear the Drupal cache (in the "Performance" section of Drupal admin).
Finally, we can test. Go to <code>http://[server]/[drupal_directory]/helloworld?echo=42</code>. You should see a page with "Hello world!" displayed.

Step 4: Setting the page title
------------------------------

In order to set the page title, you can use the `setTitle` method on the template.

```php
$this->template->setTitle("My Title");
```

Step 5: Adding libraries
------------------------

Unlike other Mouf libraries, Druplash 8 does not support the use of the WebLibraryManager (Drupal 8 does not offer a proper way to add JS/CSS files on the fly).

Instead, if you want to add JS/CSS files to your page, you will need to create a [dedicated Drupal library](https://www.drupal.org/docs/8/creating-custom-modules/adding-stylesheets-css-and-javascript-js-to-a-drupal-8-module) (you might need to create a Drupal module to host your library if you haven't one already).

Then, to add the library, use:

```php
$this->template->addLibrary("my_module_name/my_library_name");
```

In the next tutorial
--------------------

Le's move on to the next chapter, where we will [learn how to write into Drupal blocks](blocks.md).