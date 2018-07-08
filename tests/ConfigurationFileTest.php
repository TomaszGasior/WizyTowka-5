<?php

/**
* WizyTówka 5 — unit test
*/
namespace WizyTowka\UnitTests;
use WizyTowka as __;

class ConfigurationFileTest extends TestCase
{
	private const EXAMPLE_DATA = array(
		'setting1' => 'value1',
		'setting2' => 'value2',
		'setting3' => 'value3',
		'setting4' => 'value4',
		'setting5' => 'value5',
	);

	private const EXAMPLE_CONFIG_FILE = TEMP_FILES_DIR . '/ConfigurationFile_settings.conf';

	static public function tearDownAfterClass() : void
	{
		unlink(self::EXAMPLE_CONFIG_FILE);
	}

	public function testCreateNew() : void
	{
		__\ConfigurationFile::createNew(self::EXAMPLE_CONFIG_FILE);

		$current  = file_get_contents(self::EXAMPLE_CONFIG_FILE);
		$expected = json_encode([]);
		$this->assertEquals($expected, $current);
	}

	public function testWrite() : void
	{
		$config = new __\ConfigurationFile(self::EXAMPLE_CONFIG_FILE);
		foreach (self::EXAMPLE_DATA as $key => $value) {
			$config->$key = $value;
		}
		$config->save();

		$current  = json_decode(file_get_contents(self::EXAMPLE_CONFIG_FILE), true);  // "true" means associative array.
		$expected = self::EXAMPLE_DATA;
		$this->assertEquals($expected, $current);
	}

	/**
	* @expectedException     WizyTowka\ConfigurationFileException
	* @expectedExceptionCode 3
	*/
	public function testWriteWhenReadOnly() : void
	{
		$config = new __\ConfigurationFile(self::EXAMPLE_CONFIG_FILE, true);

		$config->setting1 = '';
	}

	public function testRead() : void
	{
		$config = new __\ConfigurationFile(self::EXAMPLE_CONFIG_FILE);

		foreach ($config as $key => $value) {
			$current  = $value;
			$expected = self::EXAMPLE_DATA[$key];
			$this->assertEquals($expected, $current);
		}
	}

	public function testCountable() : void
	{
		$config = new __\ConfigurationFile(self::EXAMPLE_CONFIG_FILE);

		$current  = count($config);
		$expected = 5;
		$this->assertEquals($expected, $current);
	}

	public function testReferences() : void
	{
		$config_firstInstance  = new __\ConfigurationFile(self::EXAMPLE_CONFIG_FILE);
		$config_secondInstance = new __\ConfigurationFile(self::EXAMPLE_CONFIG_FILE);

		$config_firstInstance->setting1  = strrev(self::EXAMPLE_DATA['setting1']);
		$config_secondInstance->setting2 = strtolower(self::EXAMPLE_DATA['setting2']);

		$current  = $config_secondInstance->setting1;
		$expected = $config_firstInstance->setting1;
		$this->assertEquals($expected, $current);

		$current  = $config_firstInstance->setting2;
		$expected = $config_secondInstance->setting2;
		$this->assertEquals($expected, $current);
	}
}