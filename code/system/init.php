<?php

/**
* WizyTówka 5
* Basic configuration and system initialization.
*/
namespace WizyTowka;


const VERSION        = '5.00';
const VERSION_DATE   = '2016-09-01';
const VERSION_STABLE = false;

if (PHP_VERSION_ID < 50600) {
	exit('WizyTówka content management system cannot be started. PHP 5.6 is required.');
}
if (PHP_VERSION_ID < 70000) {
	include __DIR__ . '/compat.php';
}

mb_internal_encoding('UTF-8');

include __DIR__ . '/classes/Autoloader.php';
spl_autoload_register(__NAMESPACE__.'\Autoloader::autoload');
Autoloader::addNamespace(__NAMESPACE__, __DIR__.'/classes');

set_error_handler(__NAMESPACE__.'\ErrorHandler::handleError');
set_exception_handler(__NAMESPACE__.'\ErrorHandler::handleException');


$init = function($baseController)
{
	defined(__NAMESPACE__.'\INIT') ? exit : define(__NAMESPACE__.'\INIT', 1);

	$runController = function(Controller $controller)
	{
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$controller->filterPOSTData();
			$controller->POSTQuery();
		}

		$controller->output();
	};

	/* Installer. */
	if (!file_exists(CONFIG_DIR)) {
		$controllerClass = __NAMESPACE__.'\Installer';
		$runController(new $controllerClass);
		return;
	}

	/* Initialize plugins. */
	foreach (Plugin::getAll() as $plugin) {
		$plugin->init();
	}

	$settings = Settings::get();

	/* Error handler. */
	if (!$settings->systemShowErrors) {
		ErrorHandler::showErrorDetails(false);
	}

	/* PHP settings. */
	setlocale(LC_ALL, explode('|', $settings->phpLocalesList));
	date_default_timezone_set($settings->phpTimeZone);

	/* Database connection. */
	Database::connect(
		$settings->databaseType,
		($settings->databaseType == 'sqlite') ? CONFIG_DIR.'/database.db' : $settings->databaseName,
		$settings->databaseHost, $settings->databaseUsername, $settings->databasePassword
	);

	/* User session manager. */
	SessionManager::setup();

	/* Controller. */
	$baseController  = __NAMESPACE__ . '\\'. $baseController;
	$controllerClass = $baseController::getControllerClass();
	$runController(new $controllerClass);
};