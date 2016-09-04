<?php

/**
* WizyTówka 5
* Class for various configurations stored in JSON files.
*/
namespace WizyTowka;

class ConfigurationFile implements \IteratorAggregate
{
	private $_filename;
	private $_configuration = array();
	private $_wasChanged = false;

	public function __construct($filename)
	{
		$this->_filename = $filename;
		$this->_configuration = json_decode( file_get_contents($filename), true );
	}

	public function __destruct()
	{
		if ($this->_wasChanged) {
			file_put_contents(
				$this->_filename,
				json_encode($this->_configuration, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK | JSON_UNESCAPED_UNICODE),
				LOCK_EX
			);
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

	public function __debugInfo() // For var_dump() since PHP 5.6.
	{
		return $this->_configuration;
	}

	public function getIterator() // For IteratorAggregate interface.
	{
		return new ArrayIterator($this->_configuration);
	}

	static public function createNew($filename)
	{
		file_put_contents($filename, '{}');
	}
}