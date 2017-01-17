<?php

/**
* WizyTówka 5
* Basic configuration and system initiation.
*/
namespace WizyTowka;


const VERSION        = '5.00';
const VERSION_DATE   = '2016-09-01';
const VERSION_STABLE = false;

if (PHP_VERSION_ID < 50600) {
	exit('WizyTówka content management system cannot be started. PHP 5.6 is required.');
}

setlocale(LC_ALL, 'pl_PL.UTF-8', 'pl');
date_default_timezone_set('Europe/Warsaw');
mb_internal_encoding('UTF-8');

include __DIR__ . '/classes/Autoloader.php';
spl_autoload_register(__NAMESPACE__.'\\Autoloader::autoload');
Autoloader::addNamespace(__NAMESPACE__, __DIR__.'/classes');

set_error_handler(__NAMESPACE__.'\\ErrorHandler::handleError');
set_exception_handler(__NAMESPACE__.'\\ErrorHandler::handleException');


if (defined(__NAMESPACE__.'\\INIT')) {
	call_user_func(function(){

		$runController = function(Controller $controller) {
			if ($_SERVER['REQUEST_METHOD'] == 'POST') {
				$controller->filterPOSTData();
				$controller->POSTQuery();
			}

			$controller->output();
		};

		/* Installer. */
		if (!file_exists(DATA_DIR)) {
			$runController('Installer');
			return;
		}

		/* Database connection. */
		if (Settings::get('databaseType') == 'sqlite') {
			Database::connect('sqlite', CONFIG_DIR.'/database.db');
		}
		else {
			Database::connect(Settings::get('databaseType'), Settings::get('databaseName'), Settings::get('databaseHost'), Settings::get('databaseUsername'), Settings::get('databasePassword'));
		}

		/* User session manager. */
		SessionManager::setup();

		/* Controller. */
		$controllerName = defined(__NAMESPACE__.'\\ADMIN_PANEL') ? AdminPanel::getControllerClass() : __NAMESPACE__.'\\Website';
		$runController(new $controllerName);

	});
}