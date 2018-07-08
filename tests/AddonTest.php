<?php

/**
* WizyTówka 5 — unit test
*/
namespace WizyTowka\UnitTests;
use WizyTowka as __;

class AddonTest extends TestCase
{
	private const EXAMPLE_TYPE_DIR_SYSTEM = __\SYSTEM_DIR . '/addons/exampleAddonType';
	private const EXAMPLE_TYPE_DIR_DATA   = __\DATA_DIR   . '/addons/exampleAddonType';
	private const EXAMPLE_TYPE_URL_SYSTEM = __\SYSTEM_URL . '/addons/exampleAddonType';
	private const EXAMPLE_TYPE_URL_DATA   = __\DATA_URL   . '/addons/exampleAddonType';

	static private $_exampleTypeClass;

	static public function setUpBeforeClass() : void
	{
		// Class of example addon type extending Addon class.
		self::$_exampleTypeClass = get_class(new class() extends __\Addon
		{
			static protected $_addonsSubdir = 'exampleAddonType';
			static protected $_defaultConfig = [
				'setting_1' => 1,
				'setting_2' => 2,
				'setting_3' => 3,
			];

			// Dirty hack. Addon class has private constructor but it must be public to create anonymous class.
			public function __construct() {}
		});
	}

	public function setUp() : void
	{
		// Example addons subdirectories and "addon.conf" files.
		$exampleAddonsSubdirs = [
			self::EXAMPLE_TYPE_DIR_DATA   . '/dataAddon',
			self::EXAMPLE_TYPE_DIR_SYSTEM . '/systemAddon',
			self::EXAMPLE_TYPE_DIR_DATA   . '/nameCollision',
			self::EXAMPLE_TYPE_DIR_SYSTEM . '/nameCollision',
		];
		foreach ($exampleAddonsSubdirs as $directory) {
			self::makeDirRecursive($directory);
			__\ConfigurationFile::createNew($directory . '/addon.conf');
		}
	}

	public function tearDown() : void
	{
		self::removeDirRecursive(self::EXAMPLE_TYPE_DIR_SYSTEM);
		self::removeDirRecursive(__\DATA_DIR);
	}

	public function testGetByName() : void
	{
		$dataAddon     = self::$_exampleTypeClass::getByName('dataAddon');
		$systemAddon   = self::$_exampleTypeClass::getByName('systemAddon');
		$nameCollision = self::$_exampleTypeClass::getByName('nameCollision');

		// Addon from user addons directory.
		$this->assertInstanceOf(self::$_exampleTypeClass, $dataAddon);
		$this->assertEquals('dataAddon', $dataAddon->getName());
		$this->assertTrue($dataAddon->isFromUser());

		// Addon from system addons directory.
		$this->assertInstanceOf(self::$_exampleTypeClass, $systemAddon);
		$this->assertEquals('systemAddon', $systemAddon->getName());
		$this->assertTrue($systemAddon->isFromSystem());

		// Name collision: addon "nameCollision" exists in user addons directory and in system addons directory.
		// User addon has higher priority.
		$this->assertInstanceOf(self::$_exampleTypeClass, $nameCollision);
		$this->assertEquals('nameCollision', $nameCollision->getName());
		$this->assertFalse($nameCollision->isFromSystem());
		$this->assertTrue($nameCollision->isFromUser());
	}

	public function testGetAll() : void
	{
		$current = self::$_exampleTypeClass::getAll();
		$expected = [
			self::$_exampleTypeClass::getByName('dataAddon'),
			self::$_exampleTypeClass::getByName('nameCollision'),
			self::$_exampleTypeClass::getByName('systemAddon'),
		];
		$this->assertEquals($expected, $current);
	}

	public function testGetPath() : void
	{
		$dataAddon   = self::$_exampleTypeClass::getByName('dataAddon');
		$systemAddon = self::$_exampleTypeClass::getByName('systemAddon');

		$current  = $dataAddon->getPath();
		$expected = self::EXAMPLE_TYPE_DIR_DATA . '/dataAddon';
		$this->assertEquals($expected, $current);

		$current  = $systemAddon->getPath();
		$expected = self::EXAMPLE_TYPE_DIR_SYSTEM . '/systemAddon';
		$this->assertEquals($expected, $current);
	}

	public function testGetURL() : void
	{
		$dataAddon   = self::$_exampleTypeClass::getByName('dataAddon');
		$systemAddon = self::$_exampleTypeClass::getByName('systemAddon');

		$current  = $dataAddon->getURL();
		$expected = self::EXAMPLE_TYPE_URL_DATA . '/dataAddon';
		$this->assertEquals($expected, $current);

		$current  = $systemAddon->getURL();
		$expected = self::EXAMPLE_TYPE_URL_SYSTEM . '/systemAddon';
		$this->assertEquals($expected, $current);
	}

	/**
	* @runInSeparateProcess
	*/
	public function testAddonSettings() : void
	{
		// This test needs to me run in separate process because ConfigurationFile class
		// (which is used by Addon class) uses its own cache for JSON files.

		// Settings of "systemAddon" addon overwriting defaults of example addon type.
		$addonConfFile = <<< 'JSON'
{
	"setting_1": 10,
	"setting_3": 30
}
JSON;
		file_put_contents(self::EXAMPLE_TYPE_DIR_SYSTEM . '/systemAddon/addon.conf', $addonConfFile);

		$systemAddon = self::$_exampleTypeClass::getByName('systemAddon');

		$current  = iterator_to_array($systemAddon);
		$expected = [
			'setting_1' => 10,
			'setting_2' => 2,
			'setting_3' => 30,
		];
		$this->assertEquals($expected, $current);
	}
}