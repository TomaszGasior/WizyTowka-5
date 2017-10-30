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
		$this->_HTMLTemplate->setTemplate('Message');

		$this->_HTMLTemplate->CSSClasses = 'iconInformation';
		$this->_HTMLTemplate->messageText = 'Jeszcze niezaimplementowane.';
	}
}