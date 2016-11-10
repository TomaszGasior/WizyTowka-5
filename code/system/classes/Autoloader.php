<?php

/**
* WizyTówka 5
* PHP classes autoloader.
*/
namespace WizyTowka;

class Autoloader
{
	static private $_directories = [];

	static public function addNamespace($namespace, $pathToClasses)
	{;
		if (isset(self::$_directories[$namespace])) {
			throw new WTException('This namespace is already registered.', 1);
		}
		self::$_directories[$namespace] = $pathToClasses;
	}

	static public function removeNamespace($namespace)
	{
		unset(self::$_directories[$namespace]);
	}

	static public function autoload($fullyQualifiedName)
	{
		if (strpos($fullyQualifiedName, '\\') === false) {
			return false;
		}
		list($class, $namespace) = array_map('strrev', explode('\\',strrev($fullyQualifiedName),2));

		if (!isset(self::$_directories[$namespace])) {
			return false;
		}

		$classPath = self::$_directories[$namespace] . '/' . $class . '.php';

		if (!file_exists($classPath)) {
			return false;
		}

		include $classPath;
		return true;
	}
}