<?php

class ConfigurationFileTest extends PHPUnit\Framework\TestCase
{
	static private $exampleData = array(
		'setting1' => 'value1',
		'setting2' => 'value2',
		'setting3' => 'value3',
		'setting4' => 'value4',
		'setting5' => 'value5',
	);
	static private $filename = 'example.conf';

	public function testCreateNew()
	{
		WizyTowka\ConfigurationFile::createNew(self::$filename);

		$current  = file_get_contents(self::$filename);
		$expected = '{}';
		$this->assertEquals($current, $expected);
	}

	public function testWrite()
	{
		$config = new WizyTowka\ConfigurationFile(self::$filename);
		foreach (self::$exampleData as $key => $value) {
			$config->$key = $value;
		}
		unset($config); // We must run destructor to save config changes.

		$current  = json_decode(file_get_contents(self::$filename), true);  // Use associative array.
		$expected = self::$exampleData;
		$this->assertEquals($current, $expected);
	}

	public function testRead()
	{
		$config = new WizyTowka\ConfigurationFile(self::$filename);

		foreach (self::$exampleData as $key => $value) {
			$current  = $config->$key;
			$expected = $value;
			$this->assertEquals($current, $expected);
		}
	}

	static public function tearDownAfterClass()
	{
		if (file_exists(self::$filename)) {
			unlink(self::$filename);
		}
	}
}