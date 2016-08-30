<?php

// Bootstrap the framework DO NOT edit this
require COREPATH.'bootstrap.php';

\Autoloader::add_classes(array(
	// Add classes you want to override here
	'Date'		 => APPPATH.'classes/core/date.php',
	'File'		 => APPPATH.'classes/core/file.php',
	'Input'		 => APPPATH.'classes/core/input.php',
	'Mail'		 => APPPATH.'classes/core/mail.php',
	'Mark'		 => APPPATH.'classes/core/mark.php',
	'Str'		 => APPPATH.'classes/core/str.php',
	'Uri'		 => APPPATH.'classes/core/uri.php',
	'Validation' => APPPATH.'classes/core/validation.php',
));

// Register the autoloader
\Autoloader::register();

/**
 * Your environment.  Can be set to any of the following:
 *
 * Fuel::DEVELOPMENT
 * Fuel::TEST
 * Fuel::STAGING
 * Fuel::PRODUCTION
 */
\Fuel::$env = \Arr::get($_SERVER, 'FUEL_ENV', \Arr::get($_ENV, 'FUEL_ENV', \Fuel::DEVELOPMENT));

// Initialize the framework with the config file.
\Fuel::init('config.php');
