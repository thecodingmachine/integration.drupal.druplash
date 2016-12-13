Installing Druplash
===================

Druplash is a component enabling the integration of the Splash MVC framework with Drupal.
In order to set-up your development environment, you will need to install first Mouf, then Drupal, then enable the required modules.

Druplash v6.x integrates with Drupal 6.
Druplash v7.x integrates with Drupal 7.
Druplash v8.x (this version) integrates with Drupal 8.

Step 1: installing packages using Composer
------------------------------------------

```sh
composer require mouf/integration.drupal.druplash ^8.0
```

If you don't know how to install a project using a *composer.json* file, head right now to the [composer website](http://getcomposer.org).

Step 2: installing Drupal
-------------------------

If you are starting from a fresh Drupal install, run the Drupal install process, by browsing to http://localhost/[yourproject]/.
Please note: you might need to *configure Drupal's .htaccess file so that short URLs are activated in Drupal*. This is usually done by configuring the "RewriteBase" parameter. It is a requirement for Druplash to work.

Step 3: enable Druplash package in Drupal
-----------------------------------------

Composer installed the druplash module in `/modules/contrib`. Now, we must enable this module.
Log-in as administrator in Drupal, go to the *module administration page* and enable Druplash.

Next step: follow the tutorial
------------------------------

Done? You are ready to go! You can now [start the tutorial](https://github.com/thecodingmachine/integration.drupal.druplash/blob/7.0/doc/tutorial.md).
