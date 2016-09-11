<?php

class AutoloaderTest extends PHPUnit\Framework\TestCase
{
	static public $reflector;
	static private $exampleNamespace = 'Namespace\\Example';

	static public function setUpBeforeClass()
	{
		self::$reflector = new ReflectionClass('WizyTowka\\Autoloader');
	}

	public function testAddNamespace()
	{
		$exampleDirectory = __DIR__;

		WizyTowka\Autoloader::addNamespace(self::$exampleNamespace, $exampleDirectory);

		$directoriesVariable = self::$reflector->getStaticProperties()['_directories'];
		$this->assertEquals($directoriesVariable[self::$exampleNamespace], $exampleDirectory);
	}

	public function testRemoveNamespace()
	{
		WizyTowka\Autoloader::removeNamespace(self::$exampleNamespace);

		$directoriesVariable = self::$reflector->getStaticProperties()['_directories'];
		$this->assertFalse(isset($directoriesVariable[self::$exampleNamespace]));
	}
}