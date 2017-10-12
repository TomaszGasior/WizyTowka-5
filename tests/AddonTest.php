<?php

/**
* WizyTówka 5 — unit test
*/
class AddonTest extends PHPUnit\Framework\TestCase
{
	static private $_addonsDirectorySystem = WizyTowka\SYSTEM_DIR . '/addons/exampleAddonType';
	static private $_addonsDirectoryData   = WizyTowka\DATA_DIR   . '/addons/exampleAddonType';

	static private $_exampleAddonsSubdirs = [];
	static private $_exampleAddonType;

	static public function setUpBeforeClass()
	{
		// Example addons directories.
		@mkdir(self::$_addonsDirectoryData);
		@mkdir(self::$_addonsDirectorySystem);

		// Example addons subdirectories and "addon.conf" files.
		self::$_exampleAddonsSubdirs = [
			self::$_addonsDirectoryData   . '/dataAddon',
			self::$_addonsDirectorySystem . '/systemAddon',
			self::$_addonsDirectoryData   . '/nameCollision',
			self::$_addonsDirectorySystem . '/nameCollision',
		];
		foreach (self::$_exampleAddonsSubdirs as $directory) {
			@mkdir($directory);
			WizyTowka\ConfigurationFile::createNew($directory . '/addon.conf');
		}

		// Example addon class that extends Addon class. PHP 7 syntax.
		self::$_exampleAddonType = new class() extends WizyTowka\Addon
		{
			static protected $_addonsSubdir = 'exampleAddonType';

			public function __construct() {}
			// Addon class has private constructor. Costructor must be public to create anonymous class.
		};
	}

	static public function tearDownAfterClass()
	{
		foreach (self::$_exampleAddonsSubdirs as $directory) {
			@unlink($directory . '/addon.conf');
			@rmdir($directory);
		}

		@rmdir(self::$_addonsDirectoryData);
		@rmdir(self::$_addonsDirectorySystem);
	}

	public function testGetByName()
	{
		$dataAddon     = self::$_exampleAddonType::getByName('dataAddon');
		$systemAddon   = self::$_exampleAddonType::getByName('systemAddon');
		$nameCollision = self::$_exampleAddonType::getByName('nameCollision');

		// Addon from user addons directory.
		$this->assertInstanceOf(get_class(self::$_exampleAddonType), $dataAddon);
		$this->assertEquals('dataAddon', $dataAddon->getName());
		$this->assertTrue($dataAddon->isFromUser());

		// Addon from system addons directory.
		$this->assertInstanceOf(get_class(self::$_exampleAddonType), $systemAddon);
		$this->assertEquals('systemAddon', $systemAddon->getName());
		$this->assertTrue($systemAddon->isFromSystem());

		// Name collision: addon "nameCollision" exists in user addons directory and in system addons directory.
		// If the same name is in user addons and system addons, user addon has higher priority.
		$this->assertInstanceOf(get_class(self::$_exampleAddonType), $nameCollision);
		$this->assertEquals('nameCollision', $nameCollision->getName());
		$this->assertFalse($nameCollision->isFromSystem());
		$this->assertTrue($nameCollision->isFromUser());
	}

	public function testGetAll()
	{
		$current = self::$_exampleAddonType::getAll();
		$expected = [
			self::$_exampleAddonType::getByName('dataAddon'),
			self::$_exampleAddonType::getByName('nameCollision'),
			self::$_exampleAddonType::getByName('systemAddon'),
		];
		$this->assertEquals($expected, $current);
	}

	public function testGetPath()
	{
		$dataAddon   = self::$_exampleAddonType::getByName('dataAddon');
		$systemAddon = self::$_exampleAddonType::getByName('systemAddon');

		$current  = $dataAddon->getPath();
		$expected = self::$_addonsDirectoryData . '/dataAddon';
		$this->assertEquals($expected, $current);

		$current  = $systemAddon->getPath();
		$expected = self::$_addonsDirectorySystem . '/systemAddon';
		$this->assertEquals($expected, $current);
	}
}