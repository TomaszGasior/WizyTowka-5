<?php

/**
* WizyTówka 5
* Class that renders HTML code of success or error messages.
*/
namespace WizyTowka;

class HTMLMessage
{
	private $_CSSClass;
	private $_messageDefaultText;
	private $_messageText;
	private $_messageType;

	public function __construct($CSSClass = 'message')
	{
		$this->_CSSClass = $CSSClass;
	}

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

	public function default($message)
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

	public function __toString()
	{
		if (!$this->_messageText and $this->_messageDefaultText) {
			$this->success($this->_messageDefaultText);
		}

		ob_start();

		if ($this->_messageText) {
			echo '<div class="', $this->_CSSClass ? $this->_CSSClass.' ' : '', $this->_messageType . '" role="alert">',
			     $this->_messageText, '</div>';
		}

		return ob_get_clean();
	}
}