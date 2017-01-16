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
		@unlink(self::$_filename);
	}

	public function testCreateNew()
	{
		WizyTowka\ConfigurationFile::createNew(self::$_filename);

		$current  = file_get_contents(self::$_filename);
		$expected = json_encode([]);
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

	/**
	 * @expectedException     WizyTowka\ConfigurationFileException
	 * @expectedExceptionCode 3
	 */
	public function testWriteWhenReadOnly()
	{
		$config = new WizyTowka\ConfigurationFile(self::$_filename, true);

		foreach (self::$_exampleData as $key => $value) {
			$config->$key = $value;
		}
	}

	public function testRead()
	{
		$config = new WizyTowka\ConfigurationFile(self::$_filename);

		foreach ($config as $key => $value) {
			$current  = $value;
			$expected = self::$_exampleData[$key];
			$this->assertEquals($current, $expected);
		}
	}

	public function testCountable()
	{
		$config = new WizyTowka\ConfigurationFile(self::$_filename);

		$current  = count($config);
		$expected = 5;
		$this->assertEquals($current, $expected);
	}
}