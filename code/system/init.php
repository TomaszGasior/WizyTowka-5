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
	exit('WizyTówka CMS cannot be started. PHP 5.6 is required.');
}
if (PHP_VERSION_ID < 70000) {
	include __DIR__ . '/compat.php';
}

require SYSTEM_DIR . '/classes/_Private/System.php';

function WT($controllerName = null)
{
	static $system;

	if (!$system) {
		$system = new _Private\System;
	}

	if ($controllerName) {
		return $system($controllerName);
	}

	return $system;
}

WT();