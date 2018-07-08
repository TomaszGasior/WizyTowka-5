<?php

/**
* WizyTówka 5 — unit test
*/
namespace WizyTowka\UnitTests;
use WizyTowka as __;

class AutoloaderTest extends TestCase
{
	private const EXAMPLE_NAMESPACE = 'Example\SubExample';
	private const EXAMPLE_PATH      = '/path/to/classes';

	public function testAddNamespace() : void
	{
		$autoloader = new __\_Private\Autoloader;

		$this->assertFalse(
			$autoloader->namespaceExists(self::EXAMPLE_NAMESPACE)
		);

		$this->assertTrue(
			$autoloader->addNamespace(self::EXAMPLE_NAMESPACE, self::EXAMPLE_PATH)
		);
		$this->assertFalse(
			$autoloader->addNamespace(self::EXAMPLE_NAMESPACE, self::EXAMPLE_PATH)
			// addNamespace() returns false, if namespace is already registered.
		);

		$this->assertTrue(
			$autoloader->namespaceExists(self::EXAMPLE_NAMESPACE)
		);
	}

	public function testRemoveNamespace() : void
	{
		$autoloader = new __\_Private\Autoloader;
		$autoloader->addNamespace(self::EXAMPLE_NAMESPACE, self::EXAMPLE_PATH);

		$this->assertTrue(
			$autoloader->namespaceExists(self::EXAMPLE_NAMESPACE)
		);

		$autoloader->removeNamespace(self::EXAMPLE_NAMESPACE, self::EXAMPLE_PATH);

		$this->assertFalse(
			$autoloader->namespaceExists(self::EXAMPLE_NAMESPACE)
		);
	}
}