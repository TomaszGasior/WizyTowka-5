<?php

/**
* WizyTówka 5 — unit test
*/
class AutoloaderTest extends PHPUnit\Framework\TestCase
{
	static private $_exampleNamespace = 'Example\SubExample';
	static private $_examplePath = './path/to/classes';

	public function testAddNamespace()
	{
		$this->assertFalse(
			WizyTowka\Autoloader::namespaceExists(self::$_exampleNamespace)
		);

		$this->assertTrue(
			WizyTowka\Autoloader::addNamespace(self::$_exampleNamespace, self::$_examplePath)
		);
		$this->assertFalse(
			WizyTowka\Autoloader::addNamespace(self::$_exampleNamespace, self::$_examplePath)
			// addNamespace() returns false, if namespace is already registered.
		);

		$this->assertTrue(
			WizyTowka\Autoloader::namespaceExists(self::$_exampleNamespace)
		);
	}

	public function testRemoveNamespace()
	{
		$this->assertTrue(
			WizyTowka\Autoloader::namespaceExists(self::$_exampleNamespace)
		);

		WizyTowka\Autoloader::removeNamespace(self::$_exampleNamespace, self::$_examplePath);

		$this->assertFalse(
			WizyTowka\Autoloader::namespaceExists(self::$_exampleNamespace)
		);
	}
}