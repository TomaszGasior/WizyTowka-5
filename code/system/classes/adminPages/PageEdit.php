<?php

/**
* WizyTówka 5
* Admin page — page editor.
*/
namespace WizyTowka\AdminPages;
use WizyTowka as WT;

class PageEdit extends WT\AdminPanel
{
	protected $_pageTitle = 'Edycja strony';

	protected function _output()
	{
		$this->_apTemplate->setTemplate('Message');

		$this->_apTemplate->CSSClasses = 'iInformations';
		$this->_apTemplate->messageText = 'Jeszcze niezaimplementowane.';
	}
}