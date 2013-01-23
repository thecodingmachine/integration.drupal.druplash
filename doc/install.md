Installing Druplash
===================

Druplash is a component enabling the integration of Mouf and Splash MVC framework with Drupal.
In order to set-up your development environment, you will need to install first Mouf, then Drupal, then enable the required modules.

Druplash v6.x integrates with Drupal 6.
Druplash v7.x integrates with Drupal 7.

Step 1: installing packages using Composer
------------------------------------------

Here a simple *composer.json* file that will get you started with Druplash:

	{
	    "require": {
	        "thecodingmachine/drupal": "~7.18",
	        "mouf/integration.drupal.druplash": "7.*"
	    },
	    "autoload": {
	        "psr-0": {
	            "Test": "sites/all/custom"
	        }
	    },
	    "minimum-stability": "dev",
	    "extra": {
	        "installer-paths": {
	            "sites/all/modules/druplash/": ["mouf/integration.drupal.druplash-drupalmodule"]
	        }
	    }
	}

If you don't know how to install a project using a *composer.json* file, head right now to the [composer website](http://getcomposer.org).
Note: this *composer.json* file uses the *thecodingmachine/drupal* package that installs Drupal. If you have a working Drupal install and want to add Druplash to it, simply remove the *thecodingmachine/drupal* dependency in the *composer.json* file.



Step 2: installing Mouf
-----------------------

Mouf has been downloaded as part of the dependencies.
You will still need to set-it up, by browsing to http://localhost/[yourproject]/vendor/mouf/mouf. Go to [Mouf website](http://mouf-php.com) and check out the install doc.


Step 3: installing Drupal
-------------------------

If you are starting from a fresh Drupal install, run the Drupal install process, by browsing to http://localhost/[yourproject]/.
Please note: you might need to *configure Drupal's .htaccess file so that short URLs are activated in Drupal*. This is usually done by configuring the "RewriteBase" parameter. It is a requirement for Druplash to work.

Step 4: enable Druplash package in Drupal
-----------------------------------------

Composer installed the druplash module in sites/all/modules. Now, we must enable this module.
Log-in as administrator in Drupal, go to the *module administration page* and enable Druplash.

Done? You are ready to go! You can now start the tutorial.
