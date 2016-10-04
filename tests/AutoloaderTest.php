<?php

/**
* WizyTówka 5 — unit test
*/
class AutoloaderTest extends PHPUnit\Framework\TestCase
{
	static private $_exampleNamespace = 'Namespace\\Example';

	public function testAddNamespace()
	{
		$exampleDirectory = __DIR__;

		WizyTowka\Autoloader::addNamespace(self::$_exampleNamespace, $exampleDirectory);

		$directoriesVariable = (new ReflectionClass('WizyTowka\\Autoloader'))->getStaticProperties()['_directories'];
		$this->assertEquals($directoriesVariable[self::$_exampleNamespace], $exampleDirectory);
	}

	public function testRemoveNamespace()
	{
		WizyTowka\Autoloader::removeNamespace(self::$_exampleNamespace);

		$directoriesVariable = (new ReflectionClass('WizyTowka\\Autoloader'))->getStaticProperties()['_directories'];
		$this->assertFalse(isset($directoriesVariable[self::$_exampleNamespace]));
	}
}