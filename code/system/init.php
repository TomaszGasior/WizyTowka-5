<?php

/**
* WizyTówka 5
* Basic configuration and system initialization.
*/
namespace WizyTowka;


const VERSION        = '5.00';
const VERSION_DATE   = '2016-09-01';
const VERSION_STABLE = false;

if (PHP_VERSION_ID < 50400 or !function_exists('mb_internal_encoding')
	or ini_get('register_globals') or ini_get('magic_quotes_gpc')) {
	die('WizyT&#243;wka content management system cannot be started. PHP configuration is invalid.');
}

setlocale(LC_ALL, 'pl_PL', 'pl_PL.UTF-8', 'pl');
date_default_timezone_set('Europe/Warsaw');
mb_internal_encoding('UTF-8');

include __DIR__.'/classes/Autoloader.php';
spl_autoload_register(__NAMESPACE__.'\\Autoloader::autoload');
Autoloader::addNamespace(__NAMESPACE__, __DIR__.'/classes');

set_error_handler(__NAMESPACE__.'\\ErrorHandler::convertErrorToException');
set_exception_handler(__NAMESPACE__.'\\ErrorHandler::handleException');


if (defined(__NAMESPACE__.'\\INIT')) {
	die('Comming soon…');
}