Getting started
===============

Druplash is a MVC framework for Drupal. It is actually an adaptation of Mouf's Splash MVC framework.
If you have a project developped with Splash, you can completely reuse your code, right into a Drupal installation.  

To get started, you first need to set up your environnement.

Use this *composer.json* file to install an environement with Drupal and Druplash.

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

Note: if you want to install Druplash in an existing Drupal, remove the *thecodingmachine/drupal*.