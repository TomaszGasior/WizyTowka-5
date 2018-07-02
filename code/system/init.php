<?php

/**
* WizyTówka 5
* Basic configuration and system initialization.
*/
namespace WizyTowka;


const VERSION        = '5.00';
const VERSION_DATE   = '2016-09-01';
const VERSION_STABLE = false;

if (PHP_VERSION_ID < 70000) {
	exit('Nie można uruchomić systemu WizyTówka. Wymagana wersja PHP to 7.0. Używana wersja PHP jest przestarzała.');
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