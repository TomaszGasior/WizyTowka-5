<?php

/**
* WizyTówka 5 — unit test
*/
class AddonTest extends PHPUnit\Framework\TestCase
{
	static private $_exampleAddonType;
	static private $_addonDirectorySystem;
	static private $_addonDirectoryData;

	static public function setUpBeforeClass()
	{
		// Example addons directories.
		self::$_addonDirectorySystem = WizyTowka\SYSTEM_DIR . '/addons/exampleAddonType';
		self::$_addonDirectoryData   = WizyTowka\DATA_DIR   . '/addons/exampleAddonType';
		@mkdir(self::$_addonDirectoryData);
		@mkdir(self::$_addonDirectorySystem);

		// Example addons.
		@mkdir(self::$_addonDirectoryData   . '/dataAddon');
		WizyTowka\ConfigurationFile::createNew(self::$_addonDirectoryData   . '/dataAddon/addon.conf');
		@mkdir(self::$_addonDirectorySystem . '/systemAddon');
		WizyTowka\ConfigurationFile::createNew(self::$_addonDirectorySystem . '/systemAddon/addon.conf');
		@mkdir(self::$_addonDirectoryData   . '/nameCollision');
		WizyTowka\ConfigurationFile::createNew(self::$_addonDirectoryData   . '/nameCollision/addon.conf');
		@mkdir(self::$_addonDirectorySystem . '/nameCollision');
		WizyTowka\ConfigurationFile::createNew(self::$_addonDirectorySystem . '/nameCollision/addon.conf');

		// Example addon class that extends Addon class. PHP 7 syntax.
		self::$_exampleAddonType = new class() extends WizyTowka\Addon
		{
			static protected $_addonsSubdir = 'exampleAddonType';

			public function __construct() {}
			// Oryginal Addon class has private constructor. To create anonymous class costructor must be public.
		};
	}

	public function testGetByName()
	{
		$dataAddon = self::$_exampleAddonType::getByName('dataAddon');
		$this->assertInstanceOf(get_class(self::$_exampleAddonType), $dataAddon);
		$this->assertEquals('dataAddon', $dataAddon->getName());
		$this->assertTrue($dataAddon->isFromUser());

		$systemAddon = self::$_exampleAddonType::getByName('systemAddon');
		$this->assertInstanceOf(get_class(self::$_exampleAddonType), $systemAddon);
		$this->assertEquals('systemAddon', $systemAddon->getName());
		$this->assertTrue($systemAddon->isFromSystem());

		// If the same name is in data addons and system addons, data addon has higher priority.
		$nameCollision = self::$_exampleAddonType::getByName('nameCollision');
		$this->assertInstanceOf(get_class(self::$_exampleAddonType), $nameCollision);
		$this->assertEquals('nameCollision', $nameCollision->getName());
		$this->assertFalse($nameCollision->isFromSystem());
		$this->assertTrue($nameCollision->isFromUser());
	}

	public function testGetAll()
	{
		$expected = [
			self::$_exampleAddonType::getByName('dataAddon'),
			self::$_exampleAddonType::getByName('nameCollision'),
			self::$_exampleAddonType::getByName('systemAddon'),
		];
		$current = self::$_exampleAddonType::getAll();

		$this->assertEquals($expected, $current);
	}

	static public function tearDownAfterClass()
	{
		@unlink(self::$_addonDirectoryData   . '/dataAddon/addon.conf');
		@rmdir( self::$_addonDirectoryData   . '/dataAddon');
		@unlink(self::$_addonDirectorySystem . '/systemAddon/addon.conf');
		@rmdir( self::$_addonDirectorySystem . '/systemAddon');
		@unlink(self::$_addonDirectoryData   . '/nameCollision/addon.conf');
		@rmdir( self::$_addonDirectoryData   . '/nameCollision');
		@unlink(self::$_addonDirectorySystem . '/nameCollision/addon.conf');
		@rmdir( self::$_addonDirectorySystem . '/nameCollision');

		@rmdir(self::$_addonDirectoryData);
		@rmdir(self::$_addonDirectorySystem);
	}
}