<?php

/**
* WizyTówka 5
* Read only array. Used for global variables like $_POST, $_GET, $_FILES etc.
*/
namespace WizyTowka\_Private;
use WizyTowka as __;

class ReadOnlyArray implements \ArrayAccess, \IteratorAggregate, \Countable
{
	private $_data;
	private $_name;

	public function __construct(array $data, string $name = null)
	{
		$this->_data = $data;
		$this->_name = $name;
	}

	public function __debugInfo()
	{
		return $this->_data;
	}

	public function __set(string $name, $value) : void {}  // Don't allow new properties to be set.

	public function offsetGet($key)
	{
		return $this->_data[$key];
	}

	public function offsetExists($key) : bool
	{
		return isset($this->_data[$key]);
	}

	public function offsetSet($key, $value) : void
	{
		throw ReadOnlyArrayException::iAmReadOnly($this->_name, $key);
	}

	public function offsetUnset($key) : void
	{
		throw ReadOnlyArrayException::iAmReadOnly($this->_name, $key);
	}

	public function getIterator() : iterable // For IteratorAggregate interface.
	{
		foreach ($this->_data as $key => $value) {
			yield $key => $value;
		}
	}

	public function count() : int  // For Countable interface.
	{
		return count($this->_data);
	}

	// Replaces array element. Intented for unit tests and utility scripts. Don't use it.
	public function overwrite($key, $value) : void
	{
		$this->_data[$key] = $value;
	}
}

class ReadOnlyArrayException extends __\Exception
{
	static public function iAmReadOnly($name, $key)
	{
		return new self(
			($name === null ? 'Array' : '$' . $name) .
			((is_string($key) or is_int($key)) ? '[\'' . $key . '\']' : '') .
			' is read only. It\'s forbidden to change or unset it.',
		1);
	}
}