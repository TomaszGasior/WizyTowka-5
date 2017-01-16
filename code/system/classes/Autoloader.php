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
	{
		if (isset(self::$_directories[$namespace])) {
			return false;
		}
		self::$_directories[$namespace] = $pathToClasses;
		return true;
	}

	static public function removeNamespace($namespace)
	{
		unset(self::$_directories[$namespace]);
	}

	static public function namespaceExists($namespace)
	{
		return isset(self::$_directories[$namespace]);
	}

	static public function autoload($fullyQualifiedName)
	{
		@list($class, $namespace) = array_map('strrev', explode('\\',strrev($fullyQualifiedName),2));

		if (!isset(self::$_directories[$namespace])) {
			return false;
		}

		try {
			include self::$_directories[$namespace] . '/' . $class . '.php';
			return true;
		} catch (\ErrorException $e) {
			if ($e->getFile() == __FILE__) {
				return false;
			}
			throw $e;
		}
		// If file does not exists, include command emits E_WARNING. We want to avoid file_exists() to limit operations on file system
		// for better performance. We try to include file and catch PHP error converted to \ErrorException. If error was encountered
		// in this class (this PHP file), file does not exists. Otherwise, we should throw exception again to default exception handler.
	}
}