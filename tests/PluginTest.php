<?php

/**
* WizyTówka 5 — unit test
*/
namespace WizyTowka\UnitTests;
use WizyTowka as __;

class PluginTest extends TestCase
{
	static private $_pluginsDirectory = __\SYSTEM_DIR . '/addons/plugins';
	static private $_pluginPathPart   = __\SYSTEM_DIR . '/addons/plugins/examplePlugin_';

	static private $_plugin1_addonConf = <<< 'JSON'
{
	"namespace": "__\\ExamplePlugin_1",
	"init": "PluginClass::init"
}
JSON;
	static private $_plugin1_pluginClass = <<< 'PHP'
<?php
namespace __\ExamplePlugin_1;
class PluginClass
{
	static public function init()
	{
		echo "I'm first plugin!\n";
	}
}
PHP;

	static private $_plugin2_addonConf = <<< 'JSON'
{
	"namespace": "__\\ExamplePlugin_2",
	"init": "PluginClass::run"
}
JSON;
	static private $_plugin2_pluginClass = <<< 'PHP'
<?php
namespace __\ExamplePlugin_2;
class PluginClass
{
	static public function run()
	{
		echo "I'm second plugin!\n";
	}
}
PHP;

	static public function setUpBeforeClass()
	{
		@rename(self::$_pluginsDirectory, self::$_pluginsDirectory.'.bak');
		@mkdir(self::$_pluginsDirectory);

		foreach ([1, 2] as $number) {
			@mkdir(self::$_pluginPathPart . $number);
			@mkdir(self::$_pluginPathPart . $number. '/classes');
	//
			file_put_contents(self::$_pluginPathPart.$number.'/addon.conf', self::${'_plugin'.$number.'_addonConf'});
			file_put_contents(self::$_pluginPathPart.$number.'/classes/PluginClass.php', self::${'_plugin'.$number.'_pluginClass'});
		}
	}

	static public function tearDownAfterClass()
	{
		foreach ([1, 2] as $number) {
			@unlink(self::$_pluginPathPart.$number.'/addon.conf');
			@unlink(self::$_pluginPathPart.$number.'/classes/PluginClass.php');

			@rmdir(self::$_pluginPathPart.$number.'/classes');
			@rmdir(self::$_pluginPathPart.$number);
		}

		@rmdir(self::$_pluginsDirectory);
		@rename(self::$_pluginsDirectory.'.bak', self::$_pluginsDirectory);
	}

	public function testInit()
	{
		foreach (__\Plugin::getAll() as $plugin) {
			$plugin->init();
		}

		$expected = "I'm first plugin!\nI'm second plugin!\n";
		$this->expectOutputString($expected);
	}
}