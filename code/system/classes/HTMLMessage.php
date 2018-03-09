<?php

/**
* WizyTówka 5
* Class that renders HTML code of success or error messages.
*/
namespace WizyTowka;

class HTMLMessage extends HTMLTag
{
	protected $_CSSClass = 'message';

	private $_messageDefaultText;
	private $_messageText;
	private $_messageType;

	public function __debugInfo()
	{
		return [
			'type'    => $this->_messageType,
			'text'    => $this->_messageText,
			'default' => $this->_messageDefaultText,
		];
	}

	private function _prepareText($message, ...$arguments)
	{
		$arguments = array_map(__NAMESPACE__ . '\HTML::escape', $arguments);
		return $arguments ? sprintf($message, ...$arguments) : $message;
	}

	public function success($message, ...$arguments)
	{
		$this->_messageText = $this->_prepareText($message, ...$arguments);
		$this->_messageType = __FUNCTION__;
	}

	public function error($message, ...$arguments)
	{
		$this->_messageText = $this->_prepareText($message, ...$arguments);
		$this->_messageType = __FUNCTION__;
	}

	public function info($message, ...$arguments)
	{
		$this->_messageText = $this->_prepareText($message, ...$arguments);
		$this->_messageType = __FUNCTION__;
	}

	public function information(...$arguments)
	{
		return $this->info(...$arguments);
	}

	private function _default($message, ...$arguments)
	{
		$this->_messageDefaultText = $this->_prepareText($message, ...$arguments);
	}

	// Dirty hack used to keep compatibility with PHP 5.6, where it's impossible to define method called "default".
	// More here: https://wiki.php.net/rfc/context_sensitive_lexer
	public function __call($functionName, $functionArguments)
	{
		if ($functionName == 'default') {
			return $this->_default(...$functionArguments);
		}
		trigger_error('Call to undefined method ' . static::class . '::' . $functionName . '().', E_USER_ERROR);
	}

	public function clear($default = false)
	{
		$this->_messageText = null;
		$this->_messageType = null;

		if ($default) {
			$this->_messageDefaultText = null;
		}
	}

	public function output()
	{
		if (!$this->_messageText and $this->_messageDefaultText) {
			$this->success($this->_messageDefaultText);
		}

		if ($this->_messageText) {
			echo '<div class="', ($this->_CSSClass ? $this->_CSSClass . ' ' : ''), $this->_messageType . '" role="alert">',
			     $this->_messageText, '</div>';
		}
	}
}