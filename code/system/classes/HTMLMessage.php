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

	private $_messageWasShown = false;

	public function __construct(string $CSSClass = null, ?string $messageBoxName = null, ...$arguments)
	{
		parent::__construct($CSSClass, ...$arguments);

		if ($messageBoxName != '') {
			// If $messageBoxName is present and user is logged in, HTMLMessage will try to save
			// not shown message text inside user session data and restore it in next request.
			// This is useful when message have to be shown after redirection.
			$this->_restorePreviousMessage($messageBoxName);
		}
	}

	private function _restorePreviousMessage(string $messageBoxName) : void
	{
		$session  = WT()->session;
		$dataName = 'HTMLMessage_' . $messageBoxName;

		if (!$session->isUserLoggedIn()) {
			return;
		}

		if ($data = $session->getExtraData($dataName)) {
			list($this->_messageType, $this->_messageText, $this->_messageDefaultText) = $data;
			$session->setExtraData($dataName, null);
		}

		$keepNotShownMessageForNextRequest = function() use ($session, $dataName)
		{
			if (!$this->_messageWasShown and $session->isUserLoggedIn()) {
				$data = [$this->_messageType, $this->_messageText, $this->_messageDefaultText];

				if (array_filter($data)) {
					$session->setExtraData($dataName, $data);
				}
			}
		};

		WT()->hooks->addAction('Shutdown', $keepNotShownMessageForNextRequest);
		// Don't try to use __destruct() instead. Order in which destructors are run is inconsistent,
		// especially when HTTP redirection is needed — sometimes session extra data is not saved properly.
	}

	public function __debugInfo() : array
	{
		return [
			'type'    => $this->_messageType,
			'text'    => $this->_messageText,
			'default' => $this->_messageDefaultText,
		];
	}

	private function _prepareText(string $message, ...$arguments) : string
	{
		$arguments = array_map(__NAMESPACE__ . '\HTML::escape', $arguments);
		return $arguments ? sprintf($message, ...$arguments) : $message;
	}

	public function success(string $message, ...$arguments) : void
	{
		$this->_messageText = $this->_prepareText($message, ...$arguments);
		$this->_messageType = __FUNCTION__;
	}

	public function error(string $message, ...$arguments) : void
	{
		$this->_messageText = $this->_prepareText($message, ...$arguments);
		$this->_messageType = __FUNCTION__;
	}

	public function info(string $message, ...$arguments) : void
	{
		$this->_messageText = $this->_prepareText($message, ...$arguments);
		$this->_messageType = __FUNCTION__;
	}

	public function information(...$arguments) : void
	{
		$this->info(...$arguments);
	}

	public function default(string $message, ...$arguments) : void
	{
		$this->_messageDefaultText = $this->_prepareText($message, ...$arguments);
	}

	public function clear(bool $default = false) : void
	{
		$this->_messageText = null;
		$this->_messageType = null;

		if ($default) {
			$this->_messageDefaultText = null;
		}
	}

	public function output() : void
	{
		$this->_messageWasShown = true;

		if (!$this->_messageText and $this->_messageDefaultText) {
			$this->success($this->_messageDefaultText);
		}

		if ($this->_messageText) {
			echo '<div class="', ($this->_CSSClass ? $this->_CSSClass . ' ' : ''), $this->_messageType . '" role="alert">',
			     $this->_messageText, '</div>';
		}
	}
}