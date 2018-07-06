<?php

/**
* WizyTówka 5
* Abstract addon class. Plugins, content types, themes are addons.
*/
namespace WizyTowka;

abstract class Addon implements \IteratorAggregate
{
	static protected $_addonsSubdir = '';
	static protected $_defaultConfig = [];

	private $_name;
	private $_config;
	private $_isFromSystem;

	private function __construct() {}

	private function __clone() {}

	public function __get(string $name)
	{
		return $this->_config[$name];
	}

	public function __set(string $name, $value) : void {}  // Overwriting properties is impossible.

	public function __isset(string $name) : bool
	{
		return isset($this->_config[$name]);
	}

	public function __debugInfo() : array
	{
		return [
			'name'   => $this->_name,
			'system' => $this->_isFromSystem,
			'config' => $this->_config,
		];
	}

	public function getIterator() : iterable // For IteratorAggregate interface.
	{
		foreach ($this->_config as $key => $value) {
			yield $key => $value;
		}
	}

	public function getName() : string
	{
		return $this->_name;
	}

	public function getPath() : string
	{
		return ($this->_isFromSystem ? SYSTEM_DIR : DATA_DIR) . '/addons/' . static::$_addonsSubdir . '/' . $this->_name;
	}

	public function getURL() : string
	{
		return ($this->_isFromSystem ? SYSTEM_URL : DATA_URL) . '/addons/' . static::$_addonsSubdir . '/' . $this->_name;
	}

	public function isFromUser() : bool
	{
		return !$this->_isFromSystem;
	}

	public function isFromSystem() : bool
	{
		return $this->_isFromSystem;
	}

	static public function getByName(string $name) : ?self
	{
		$configPath = '/addons/' . static::$_addonsSubdir . '/' . $name . '/addon.conf';

		// Check first in data folder allowing to override system addons.
		if (($isFromUser = file_exists(DATA_DIR . $configPath)) or file_exists(SYSTEM_DIR . $configPath)) {
			$addonObject = new static;

			$addonObject->_name = $name;
			$addonObject->_isFromSystem = !$isFromUser;

			$addonObject->_config = iterator_to_array(
				new ConfigurationFile(($isFromUser ? DATA_DIR : SYSTEM_DIR) . $configPath)
			) + static::$_defaultConfig;

			return $addonObject;
		}

		return null;
	}

	static public function getAll() : array
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