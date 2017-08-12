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

	public function __debugInfo()
	{
		return [
			'name' => $this->_name,
			'isFromSystem' => $this->_isFromSystem,
			'config' => $this->_config,
		];
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
		$configFilePath = '/addons/' . static::$_addonsSubdir . '/' . $name . '/addon.conf';
		$addonObject = new static;

		if (file_exists(DATA_DIR.$configFilePath)) {
			$addonObject->_config = new ConfigurationFile(DATA_DIR.$configFilePath, true); // Read only.
			$addonObject->_name = $name;
			$addonObject->_isFromSystem = false;

			return $addonObject;
		}
		elseif (file_exists(SYSTEM_DIR.$configFilePath)) {
			$addonObject->_config = new ConfigurationFile(SYSTEM_DIR.$configFilePath, true); // Read only.
			$addonObject->_name = $name;
			$addonObject->_isFromSystem = true;

			return $addonObject;
		}

		return false;
	}

	static public function getAll()
	{
		$addons = glob(
			'{' . DATA_DIR . ',' . SYSTEM_DIR . '}/addons/' . static::$_addonsSubdir . '/*/addon.conf',
			GLOB_BRACE
		);
		if ($addons === false) {
			// Notice: if directory is empty, glob() should return empty array,
			// but it is possible to return false on some operating systems.
			// More here: http://php.net/manual/en/function.glob.php#refsect1-function.glob-returnvalues
			return [];
		}
		$addons = array_unique(array_map(function($var){ return basename(dirname($var)); }, $addons));

		$elementsToReturn = [];
		foreach ($addons as $addonName) {
			$elementsToReturn[] = static::getByName($addonName);
		}
		return $elementsToReturn;
	}
}