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

	public function success($message)
	{
		$this->_messageText = $message;
		$this->_messageType = __FUNCTION__;
	}

	public function error($message)
	{
		$this->_messageText = $message;
		$this->_messageType = __FUNCTION__;
	}

	public function info($message)
	{
		$this->_messageText = $message;
		$this->_messageType = __FUNCTION__;
	}

	public function information(...$arguments)
	{
		return $this->info(...$arguments);
	}

	// Dirty hack used to keep compatibility with PHP 5.6, where it is impossible to define method called "default".
	// More here: https://wiki.php.net/rfc/context_sensitive_lexer
	public function __call($functionName, $functionArguments)
	{
		if ($functionName == 'default') {
			return $this->_default($functionArguments[0]);
		}
		trigger_error('Call to undefined method '.static::class.'::'.$functionName.'().', E_USER_ERROR);
	}

	private function _default($message)
	{
		$this->_messageDefaultText = $message;
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
			echo '<div class="', $this->_CSSClass ? $this->_CSSClass.' ' : '', $this->_messageType . '" role="alert">',
			     $this->_messageText, '</div>';
		}
	}
}