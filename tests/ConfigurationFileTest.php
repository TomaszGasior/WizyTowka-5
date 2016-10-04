<?php

/**
* WizyTówka 5 — unit test
*/
class ConfigurationFileTest extends PHPUnit\Framework\TestCase
{
	static private $_exampleData = array(
		'setting1' => 'value1',
		'setting2' => 'value2',
		'setting3' => 'value3',
		'setting4' => 'value4',
		'setting5' => 'value5',
	);
	static private $_filename = 'example.conf';

	static public function tearDownAfterClass()
	{
		if (file_exists(self::$_filename)) {
			unlink(self::$_filename);
		}
	}

	public function testCreateNew()
	{
		WizyTowka\ConfigurationFile::createNew(self::$_filename);

		$current  = file_get_contents(self::$_filename);
		$expected = '{}';
		$this->assertEquals($current, $expected);
	}

	public function testWrite()
	{
		$config = new WizyTowka\ConfigurationFile(self::$_filename);
		foreach (self::$_exampleData as $key => $value) {
			$config->$key = $value;
		}
		unset($config); // We must run destructor to save config changes.

		$current  = json_decode(file_get_contents(self::$_filename), true);  // Use associative array.
		$expected = self::$_exampleData;
		$this->assertEquals($current, $expected);
	}

	public function testRead()
	{
		$config = new WizyTowka\ConfigurationFile(self::$_filename);

		foreach (self::$_exampleData as $key => $value) {
			$current  = $config->$key;
			$expected = $value;
			$this->assertEquals($current, $expected);
		}
	}
}