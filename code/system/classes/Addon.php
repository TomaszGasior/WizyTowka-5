<?php

/**
* WizyTówka 5
* Abstract addon class. Plugins, content types, themes are addons.
*/
namespace WizyTowka;

abstract class Addon
{
	static protected $_addonsSubdir = '';

	private $_name;
	private $_config;
	private $_isFromSystem;

	private function __construct() {}

	public function __get($name)
	{
		return $this->_config->$name;
	}

	public function __isset($name)
	{
		return isset($this->_config->$name);
	}

	public function getName()
	{
		return $this->_name;
	}

	public function isFromUser()
	{
		return !$this->_isFromSystem;
	}

	public function isFromSystem()
	{
		return $this->_isFromSystem;
	}

	static public function getByName($name)
	{
		$thisClassName = get_called_class();
		$configFilePath = '/addons/' . static::$_addonsSubdir . '/' . $name . '/addon.conf';

		$addonObject = new $thisClassName;

		if (file_exists(DATA_DIR.$configFilePath)) {
			$addonObject->_config = new ConfigurationFile(DATA_DIR.$configFilePath);
			$addonObject->_name = $name;
			$addonObject->_isFromSystem = false;

			return $addonObject;
		}
		elseif (file_exists(SYSTEM_DIR.$configFilePath)) {
			$addonObject->_config = new ConfigurationFile(SYSTEM_DIR.$configFilePath);
			$addonObject->_name = $name;
			$addonObject->_isFromSystem = true;

			return $addonObject;
		}

		return false;
	}

	static public function getAll()
	{
		$userAddonsAndSystemAddons = [
			glob(  DATA_DIR . '/addons/' . static::$_addonsSubdir . '/*/addon.conf'),
			glob(SYSTEM_DIR . '/addons/' . static::$_addonsSubdir . '/*/addon.conf')
		];
		// Notice: if directory is empty glob() should return empty array, but it is possible to return false on some operating systems.

		$elementsToReturn = [];

		foreach ($userAddonsAndSystemAddons as $addons) {
			if (!empty($addons)) {
				foreach ($addons as $fullConfigFilePath) {
					$addonName = basename(dirname($fullConfigFilePath));
					$elementsToReturn[] = static::getByName($addonName);
				}
			}
		}

		return $elementsToReturn;
	}
}