<?php

/**
* WizyTówka 5
* Basic configuration and system initiation.
*/
namespace WizyTowka;


const VERSION        = '5.00';
const VERSION_DATE   = '2016-09-01';
const VERSION_STABLE = false;

if (PHP_VERSION_ID < 50500) {
	die('WizyTówka content management system cannot be started. PHP 5.5 is required.');
}

setlocale(LC_ALL, 'pl_PL.UTF-8', 'pl');
date_default_timezone_set('Europe/Warsaw');
mb_internal_encoding('UTF-8');

include __DIR__.'/classes/Autoloader.php';
spl_autoload_register(__NAMESPACE__.'\\Autoloader::autoload');
Autoloader::addNamespace(__NAMESPACE__, __DIR__.'/classes');

set_error_handler(__NAMESPACE__.'\\ErrorHandler::convertErrorToException');
set_exception_handler(__NAMESPACE__.'\\ErrorHandler::handleException');


if (defined(__NAMESPACE__.'\\INIT')) {
	// defined(__NAMESPACE__.'\\ADMIN_PANEL')
}