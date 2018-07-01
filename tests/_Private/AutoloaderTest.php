<?php

/**
* WizyTówka 5 — unit test
*/
namespace WizyTowka\UnitTests;
use WizyTowka as __;

class AutoloaderTest extends TestCase
{
	static private $_exampleNamespace = 'Example\SubExample';
	static private $_examplePath = './path/to/classes';

	public function testAddNamespace()
	{
		$autoloader = new __\_Private\Autoloader;

		$this->assertFalse(
			$autoloader->namespaceExists(self::$_exampleNamespace)
		);

		$this->assertTrue(
			$autoloader->addNamespace(self::$_exampleNamespace, self::$_examplePath)
		);
		$this->assertFalse(
			$autoloader->addNamespace(self::$_exampleNamespace, self::$_examplePath)
			// addNamespace() returns false, if namespace is already registered.
		);

		$this->assertTrue(
			$autoloader->namespaceExists(self::$_exampleNamespace)
		);
	}

	public function testRemoveNamespace()
	{
		$autoloader = new __\_Private\Autoloader;
		$autoloader->addNamespace(self::$_exampleNamespace, self::$_examplePath);

		$this->assertTrue(
			$autoloader->namespaceExists(self::$_exampleNamespace)
		);

		$autoloader->removeNamespace(self::$_exampleNamespace, self::$_examplePath);

		$this->assertFalse(
			$autoloader->namespaceExists(self::$_exampleNamespace)
		);
	}
}