<?php

/**
* WizyTówka 5
* This class stores ConfigurationFile instance with main configuration file.
*/
namespace WizyTowka;

class Settings
{
	static private $_settings;
	static private $_defaultSettings;

	static public function get($option = null)
	{
		if (empty(self::$_settings)) {
			try {
				self::$_settings = new ConfigurationFile(CONFIG_DIR . '/settings.conf');
			} catch (\Exception $e) {
				throw new Exception('Main configuration file is corrupted. You should check file syntax, replace this file with default or reinstall CMS.', 10);
			}
		}

		return ($option) ? self::$_settings->$option : self::$_settings;
	}

	static public function getDefault($option = null)
	{
		if (empty(self::$_defaultSettings)) {
			self::$_defaultSettings = new ConfigurationFile(SYSTEM_DIR . '/defaults/settings.conf', true); // Read only.
		}

		return ($option) ? self::$_defaultSettings->$option : self::$_defaultSettings;
	}
}