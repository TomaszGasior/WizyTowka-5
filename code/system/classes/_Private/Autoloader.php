<?php

/**
* WizyTówka 5
* PHP classes autoloader.
*/
namespace WizyTowka\_Private;

class Autoloader
{
	private $_directories = [];

	public function addNamespace(string $namespace, string $pathToClasses) : bool
	{
		if (isset($this->_directories[$namespace])) {
			return false;
		}

		$this->_directories[$namespace] = $pathToClasses;
		return true;
	}

	public function removeNamespace(string $namespace) : void
	{
		unset($this->_directories[$namespace]);
	}

	public function namespaceExists(string $namespace) : bool
	{
		return isset($this->_directories[$namespace]);
	}

	public function autoload(string $FQCN) : bool
	{
		@list($class, $namespace) = array_map('strrev', explode('\\',strrev($FQCN),2));

		if (!isset($this->_directories[$namespace])) {
			return false;
		}

		try {
			include $this->_directories[$namespace] . '/' . $class . '.php';
			return true;
		} catch (\ErrorException $e) {
			if ($e->getFile() == __FILE__) {
				return false;
			}
			throw $e;
		}
		// If file does not exists, "include" emits E_WARNING. We want to avoid file_exists() to limit operations on file system
		// for better performance. We try to include file and catch PHP error converted to \ErrorException. If error was encountered
		// in this class (this PHP file), file does not exists. Otherwise, we should throw exception again to default exception handler.
	}
}