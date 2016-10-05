<?php

/**
* WizyTówka 5
* Class for various configurations stored in JSON files.
*/
namespace WizyTowka;

class ConfigurationFile implements \IteratorAggregate
{
	private $_filename;
	private $_configuration = [];
	private $_wasChanged = false;

	public function __construct($filename)
	{
		$this->_filename = $filename;
		$this->_configuration = json_decode(file_get_contents($filename), true);  // Associative array.

		if (!is_array($this->_configuration)) {
			$this->_configuration = [];
			throw new WTException('Configuration file ' . $filename . ' does not contain array.', 12);
		}
		if (json_last_error() != JSON_ERROR_NONE) {
			$this->_configuration = [];
			throw new WTException('Error during reading JSON config file: ' . json_last_error_msg() . '.', 2);
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
				throw new WTException('Error during writing JSON config file: ' . json_last_error_msg() . '.', 3);
			}
		}
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

	public function __get($key)
	{
		return $this->_configuration[$key];
	}

	public function __set($key, $value)
	{
		$this->_wasChanged = true;
		$this->_configuration[$key] = $value;
	}

	public function s__debugInfo() // For var_dump() since PHP 5.6.
	{
		return $this->_configuration;
	}

	public function getIterator() // For IteratorAggregate interface.
	{
		return new \ArrayIterator($this->_configuration);
	}

	static public function createNew($filename)
	{
		file_put_contents($filename, json_encode([]));
	}
}


// Poor json_last_error_msg() implementation for PHP versions older than 5.5.
if (!function_exists('json_last_error_msg')) {
	function json_last_error_msg()
	{
		$JSONErrorConstants = array_filter(get_defined_constants(), function($key){
			return preg_match('/JSON_ERROR_.*/', $key);
		}, ARRAY_FILTER_USE_KEY);

		foreach ($JSONErrorConstants as $constantName => $constantValue) {
			if ($constantValue == json_last_error()) {
				return $constantName;
			}
		}

		return 'undefined';
	}
}