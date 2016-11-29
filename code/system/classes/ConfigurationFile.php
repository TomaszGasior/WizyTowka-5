<?php

/**
* WizyTówka 5
* Class for various configurations stored in JSON files.
*/
namespace WizyTowka;

class ConfigurationFile implements \IteratorAggregate, \Countable
{
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
			throw new Exception('Error during reading JSON config file: ' . json_last_error_msg() . '.', 2);
		}
		if (!is_array($this->_configuration)) {
			$this->_configuration = [];
			throw new Exception('Configuration file ' . $filename . ' does not contain array.', 4);
		}
	}

	public function __destruct()
	{
		if ($this->_wasChanged) {
			file_put_contents(
				$this->_filename,
				json_encode($this->_configuration, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK | JSON_UNESCAPED_UNICODE),
				LOCK_EX
			);

			if (json_last_error() != JSON_ERROR_NONE) {
				throw new Exception('Error during writing JSON config file: ' . json_last_error_msg() . '.', 3);
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
			throw new Exception('Configuration file ' . $this->_filename . ' is opened as read only.', 19);
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

	public function __debugInfo() // For var_dump() since PHP 5.6.
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