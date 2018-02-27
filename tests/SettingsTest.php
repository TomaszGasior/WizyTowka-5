<?php

/**
* WizyTówka 5 — unit test
*/
class SettingsTest extends TestCase
{
	static private $_settingsFilePath        = WizyTowka\CONFIG_DIR . '/settings.conf';
	static private $_defaultSettingsFilePath = WizyTowka\SYSTEM_DIR . '/defaults/settings.conf';

	static private $_exampleSettings = [
		'adminPanelDefaultPage' => 'pages',
		'phpTimeZone'           => 'Europe/Warsaw',
		'websiteDateFormat'     => '%Y-%m-%d %H:%M:%S',
		'systemVersion'         => '5.00',
	];

	static public function setUpBeforeClass()
	{
		@rename(self::$_settingsFilePath, self::$_settingsFilePath.'.bak');
		@rename(self::$_defaultSettingsFilePath, self::$_defaultSettingsFilePath.'.bak');

		WizyTowka\ConfigurationFile::createNew(self::$_settingsFilePath);
		WizyTowka\ConfigurationFile::createNew(self::$_defaultSettingsFilePath);

		$settings        = new WizyTowka\ConfigurationFile(self::$_settingsFilePath);
		$defaultSettings = new WizyTowka\ConfigurationFile(self::$_defaultSettingsFilePath);

		foreach (self::$_exampleSettings as $setting => $value) {
			$settings->$setting        = $value;
			$defaultSettings->$setting = $value;
		}
	}

	static public function tearDownAfterClass()
	{
		@unlink(self::$_settingsFilePath);
		@unlink(self::$_defaultSettingsFilePath);

		@rename(self::$_settingsFilePath.'.bak', self::$_settingsFilePath);
		@rename(self::$_defaultSettingsFilePath.'.bak', self::$_defaultSettingsFilePath);
	}

	public function testGet()
	{
		$current  = WizyTowka\Settings::get(array_keys(self::$_exampleSettings)[0]);
		$expected = array_values(self::$_exampleSettings)[0];
		$this->assertEquals($expected, $current);

		foreach (WizyTowka\Settings::get() as $setting => $value) {
			$current  = $value;
			$expected = self::$_exampleSettings[$setting];
			$this->assertEquals($expected, $current);
		}
	}

	public function testGetWriting()
	{
		$modify = function($string)
		{
			return mb_strtoupper($string) . date('Y-m-d');
		};

		$settings = WizyTowka\Settings::get();
		foreach ($settings as $key => $value) {
			$settings->$key = $modify($value);
		}

		foreach (WizyTowka\Settings::get() as $key => $value) {
			$current  = $value;
			$expected = $modify(self::$_exampleSettings[$key]);
			$this->assertEquals($expected, $current);
		}
	}

	public function testGetDefault()
	{
		$current  = WizyTowka\Settings::getDefault(array_keys(self::$_exampleSettings)[0]);
		$expected = array_values(self::$_exampleSettings)[0];
		$this->assertEquals($expected, $current);

		foreach (WizyTowka\Settings::getDefault() as $setting => $value) {
			$current  = $value;
			$expected = self::$_exampleSettings[$setting];
			$this->assertEquals($expected, $current);
		}
	}

	/**
	 * @expectedException     WizyTowka\ConfigurationFileException
	 * @expectedExceptionCode 3
	 */
	public function testGetDefaultWriting()
	{
		$defaultSettings = WizyTowka\Settings::getDefault();

		$defaultSettings->{array_keys(self::$_exampleSettings)[0]} = '';
	}
}