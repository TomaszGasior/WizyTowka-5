<?php

/**
* WizyTówka 5 — unit test
*/
class ConfigurationFileTest extends TestCase
{
	static private $_exampleData = array(
		'setting1' => 'value1',
		'setting2' => 'value2',
		'setting3' => 'value3',
		'setting4' => 'value4',
		'setting5' => 'value5',
	);
	static private $_exampleFilename = 'example.conf';

	static public function tearDownAfterClass()
	{
		@unlink(self::$_exampleFilename);
	}

	public function testCreateNew()
	{
		WizyTowka\ConfigurationFile::createNew(self::$_exampleFilename);

		$current  = file_get_contents(self::$_exampleFilename);
		$expected = json_encode([]);
		$this->assertEquals($expected, $current);
	}

	public function testWrite()
	{
		$config = new WizyTowka\ConfigurationFile(self::$_exampleFilename);
		foreach (self::$_exampleData as $key => $value) {
			$config->$key = $value;
		}
		unset($config); // Destructor of ConfigurationFile class saves config changes.

		$current  = json_decode(file_get_contents(self::$_exampleFilename), true);  // "true" means associative array.
		$expected = self::$_exampleData;
		$this->assertEquals($expected, $current);
	}

	/**
	 * @expectedException     WizyTowka\ConfigurationFileException
	 * @expectedExceptionCode 3
	 */
	public function testWriteWhenReadOnly()
	{
		$config = new WizyTowka\ConfigurationFile(self::$_exampleFilename, true);

		$config->setting1 = '';
	}

	public function testRead()
	{
		$config = new WizyTowka\ConfigurationFile(self::$_exampleFilename);

		foreach ($config as $key => $value) {
			$current  = $value;
			$expected = self::$_exampleData[$key];
			$this->assertEquals($expected, $current);
		}
	}

	public function testCountable()
	{
		$config = new WizyTowka\ConfigurationFile(self::$_exampleFilename);

		$current  = count($config);
		$expected = 5;
		$this->assertEquals($expected, $current);
	}

	public function testReferences()
	{
		$config_firstInstance  = new WizyTowka\ConfigurationFile(self::$_exampleFilename);
		$config_secondInstance = new WizyTowka\ConfigurationFile(self::$_exampleFilename);

		$config_firstInstance->setting1  = strrev(self::$_exampleData['setting1']);
		$config_secondInstance->setting2 = strtolower(self::$_exampleData['setting2']);

		$current  = $config_secondInstance->setting1;
		$expected = $config_firstInstance->setting1;
		$this->assertEquals($expected, $current);

		$current  = $config_firstInstance->setting2;
		$expected = $config_secondInstance->setting2;
		$this->assertEquals($expected, $current);
	}
}