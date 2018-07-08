<?php

/**
* WizyTówka 5 — unit test
*/
namespace WizyTowka\UnitTests;
use WizyTowka as __;

class PluginTest extends TestCase
{
	private const PLUGIN_NAME_PATTERN     = 'examplePlugin_%d';
	private const PLUGIN_DIR_PATH_PATTERN = __\DATA_DIR . '/addons/plugins/' . self::PLUGIN_NAME_PATTERN;

	private const PLUGIN_1_CONF = <<< 'JSON'
{
	"namespace": "WizyTowka\\ExamplePlugin_1",
	"init": "PluginClass::init"
}
JSON;
	private const PLUGIN_1_CLASS = <<< 'PHP'
<?php
namespace WizyTowka\ExamplePlugin_1;
class PluginClass
{
	static public function init() : void
	{
		echo "I'm first plugin!\n";
	}
}
PHP;

	private const PLUGIN_2_CONF = <<< 'JSON'
{
	"namespace": "WizyTowka\\ExamplePlugin_2",
	"init": "PluginClass::run"
}
JSON;
	private const PLUGIN_2_CLASS = <<< 'PHP'
<?php
namespace WizyTowka\ExamplePlugin_2;
class PluginClass
{
	static public function run() : void
	{
		echo "I'm second plugin!\n";
	}
}
PHP;

	static public function setUpBeforeClass() : void
	{
		foreach ([1, 2] as $number) {
			$pluginPath = sprintf(self::PLUGIN_DIR_PATH_PATTERN, $number);

			self::makeDirRecursive($pluginPath . '/classes');
			file_put_contents($pluginPath . '/addon.conf',              constant("self::PLUGIN_{$number}_CONF"));
			file_put_contents($pluginPath . '/classes/PluginClass.php', constant("self::PLUGIN_{$number}_CLASS"));
		}
	}

	static public function tearDownAfterClass() : void
	{
		self::removeDirRecursive(__\DATA_DIR);
	}

	public function testInit() : void
	{
		foreach ([1, 2] as $number) {
			$pluginName = sprintf(self::PLUGIN_NAME_PATTERN, $number);

			__\Plugin::getByName($pluginName)->init();
		}

		$expected = "I'm first plugin!\nI'm second plugin!\n";
		$this->expectOutputString($expected);
	}
}