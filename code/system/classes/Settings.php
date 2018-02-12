<?php

/**
* WizyTówka 5
* Settings manager. Stores ConfigurationFile instance with main configuration file.
*/
namespace WizyTowka;

trait Settings
{
	static private $_settings;
	static private $_defaultSettings;

	static public function get($option = null)
	{
		if (!self::$_settings) {
			self::$_settings = new ConfigurationFile(CONFIG_DIR . '/settings.conf');
		}

		return ($option) ? self::$_settings->$option : self::$_settings;
	}

	static public function getDefault($option = null)
	{
		if (!self::$_defaultSettings) {
			self::$_defaultSettings = new ConfigurationFile(SYSTEM_DIR . '/defaults/settings.conf', true); // Read only.
		}

		return ($option) ? self::$_defaultSettings->$option : self::$_defaultSettings;
	}
}