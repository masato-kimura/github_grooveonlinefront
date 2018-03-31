<?php
use Fuel\Core\Autoloader;
// Load in the Autoloader
require COREPATH.'classes'.DIRECTORY_SEPARATOR.'autoloader.php';
class_alias('Fuel\\Core\\Autoloader', 'Autoloader');

// Bootstrap the framework DO NOT edit this
require COREPATH.'bootstrap.php';

Autoloader::add_namespace('Lastfm', APPPATH. 'vendor/php-last.fm-api-master/src/', true);
Autoloader::add_namespace('Lastfm\Caller', APPPATH. 'vendor/php-last.fm-api-master/src/caller/', true);
Autoloader::add_namespace('Lastfm\Cache', APPPATH. 'vendor/php-last.fm-api-master/src/cache/', true);


Autoloader::add_classes(array(
	// Add classes you want to override here
	// Example: 'View' => APPPATH.'classes/view.php',
	'Log' => APPPATH. 'classes/log.php',
	'Agent' => APPPATH. 'classes/agent.php',
));

// Register the autoloader
Autoloader::register();

/**
 * Your environment.  Can be set to any of the following:
 *
 * Fuel::DEVELOPMENT
 * Fuel::TEST
 * Fuel::STAGING
 * Fuel::PRODUCTION
 */

Fuel::$env = (isset($_SERVER['FUEL_ENV']) ? $_SERVER['FUEL_ENV'] : Fuel::DEVELOPMENT);
//Fuel::$env = (isset($_SERVER['FUEL_ENV']) ? $_SERVER['FUEL_ENV'] : Fuel::PRODUCTION);
//Fuel::$env = (isset($_SERVER['FUEL_ENV']) ? $_SERVER['FUEL_ENV'] : Fuel::STAGING);
//\Log::debug(Fuel::$env);
//Fuel::$env = Fuel::DEVELOPMENT;
//Fuel::$env = Fuel::STAGING;
//Fuel::$env = Fuel::PRODUCTION;
// Initialize the framework with the config file.
Fuel::init('config.php');
