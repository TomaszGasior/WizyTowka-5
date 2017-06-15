<?php

/**
* WizyTówka 5
* Class for various configurations stored in JSON files.
*/
namespace WizyTowka;

class ConfigurationFile implements \IteratorAggregate, \Countable
{
	static private $_configurationFiles = [];

	private $_filename;
	private $_configuration = [];
	private $_wasChanged = false;
	private $_readOnly = false;

	public function __construct($filename, $readOnly = false)
	{
		$this->_filename = $filename;
		$this->_readOnly = (boolean)$readOnly;

		$filenameHash = md5(($filename[0] == '/') ? $filename : realpath($filename));
		// It is a hash of full path of configuration file.
		// We should avoid realpath() when it is possible to limit operations on file system.

		if (!isset(self::$_configurationFiles[$filenameHash])) {
			$configuration = json_decode(file_get_contents($filename), true);  // "true" means associative array.

			if (json_last_error() != JSON_ERROR_NONE) {
				throw ConfigurationFileException::JSONError($filename);
			}
			if (!is_array($configuration)) {
				throw ConfigurationFileException::invalidArray($filename);
			}

			self::$_configurationFiles[$filenameHash] = $configuration;
		}

		// If there is more than one instance of ConfigurationFile class that opens the same file,
		// configuration changes will not be overwritten and each instance of ConfigurationFile class
		// will use current configuration without reading file from file system more than once.
		$this->_configuration =& self::$_configurationFiles[$filenameHash];
	}

	public function __destruct()
	{
		if ($this->_wasChanged) {
			file_put_contents(
				$this->_filename,
				json_encode($this->_configuration, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
				LOCK_EX
			);

			if (json_last_error() != JSON_ERROR_NONE) {
				throw ConfigurationFileException::JSONError($this->_filename);
			}
		}
	}

	public function __get($key)
	{
		return $this->_configuration[$key];
	}

	public function __set($key, $value)
	{
		if ($this->_readOnly) {
			throw ConfigurationFileException::writingWhenReadOnly($this->_filename);
		}

		$this->_wasChanged = true;
		$this->_configuration[$key] = $value;
	}

	public function __isset($key)
	{
		return isset($this->_configuration[$key]);
	}

	public function __unset($key)
	{
		$this->_wasChanged = true;
		unset($this->_configuration[$key]);
	}

	public function __debugInfo()
	{
		return $this->_configuration;
	}

	public function &getIterator() // For IteratorAggregate interface.
	{
		foreach ($this->_configuration as $key => &$value) {
			yield $key => $value;
		}
		// Reference is used to allow foreach syntax like it: foreach($object as &$value) { ... }.
	}

	public function count()  // For Countable interface.
	{
		return count($this->_configuration);
	}

	static public function createNew($filename)
	{
		file_put_contents($filename, json_encode([]));
	}
}

class ConfigurationFileException extends Exception
{
	static public function JSONError($filename)
	{
		return new self('Error "' . json_last_error_msg() . '" during JSON operation on configuration file: ' . $filename . '.', 1);
	}
	static public function invalidArray($filename)
	{
		return new self('Configuration file ' . $filename . ' does not contain array.', 2);
	}
	static public function writingWhenReadOnly($filename)
	{
		return new self('Configuration file ' . $filename . ' is opened as read only.', 3);
	}
}