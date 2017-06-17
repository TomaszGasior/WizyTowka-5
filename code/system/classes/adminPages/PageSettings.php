<?php

/**
* WizyTówka 5
* Admin page — page settings.
*/
namespace WizyTowka\AdminPages;
use WizyTowka as WT;

class PageSettings extends WT\AdminPanel
{
	protected $_pageTitle = 'Ustawienia strony';

	protected function _output()
	{
		$this->_apTemplate->setTemplate('Message');

		$this->_apTemplate->CSSClasses = 'iInformations';
		$this->_apTemplate->messageText = 'Jeszcze niezaimplementowane.';
	}
}