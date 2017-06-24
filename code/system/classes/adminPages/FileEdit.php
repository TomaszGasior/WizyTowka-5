<?php

/**
* WizyTówka 5
* Admin page — file editor.
*/
namespace WizyTowka\AdminPages;
use WizyTowka as WT;

class FileEdit extends WT\AdminPanel
{
	protected $_pageTitle = 'Edycja pliku';

	protected function _output()
	{
		$this->_apTemplate->setTemplate('Message');

		$this->_apTemplate->CSSClasses = 'iconInformation';
		$this->_apTemplate->messageText = 'Jeszcze niezaimplementowane.';
	}
}