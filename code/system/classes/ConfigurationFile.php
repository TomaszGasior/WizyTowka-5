<?php

/**
* WizyTówka 5
* Class for various configurations stored in JSON files.
*/
namespace WizyTowka;

class ConfigurationFile implements \IteratorAggregate, \Countable
{
	static private $_modifiedFiles = [];

	private $_filename;
	private $_configuration = [];
	private $_wasChanged = false;
	private $_readOnly = false;

	public function __construct($filename, $readOnly = false)
	{
		$this->_filename = $filename;
		$this->_readOnly = (boolean)$readOnly;
		$this->_configuration = json_decode(file_get_contents($filename), true);  // Associative array.

		if (json_last_error() != JSON_ERROR_NONE) {
			$this->_configuration = [];
			throw ConfigurationFileException::JSONError($filename);
		}
		if (!is_array($this->_configuration)) {
			$this->_configuration = [];
			throw ConfigurationFileException::invalidArray($filename);
		}
	}

	public function __destruct()
	{
		if ($this->_wasChanged) {
			// If there is more than one instance of ConfigurationFile class that makes changes in  the same file,
			// configuration changes could be overwritten. Code below prevents from it.
			if (in_array($this->_filename, self::$_modifiedFiles)) {
				throw ConfigurationFileException::modificationCollision($this->_filename, self::class);
			}
			self::$_modifiedFiles[] = $this->_filename;

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
	static public function modificationCollision($filename, $class)
	{
		return new self('Configuration file ' . $filename . ' was modified by more than one instance of ' . $class . ' class.', 4);
	}
}