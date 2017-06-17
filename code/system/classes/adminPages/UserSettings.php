<?php

/**
* WizyTówka 5
* Admin page — user settings.
*/
namespace WizyTowka\AdminPages;
use WizyTowka as WT;

class UserSettings extends WT\AdminPanel
{
	protected $_pageTitle = 'Ustawienia użytkownika';

	protected function _output()
	{
		$this->_apTemplate->setTemplate('Message');

		$this->_apTemplate->CSSClasses = 'iInformations';
		$this->_apTemplate->messageText = 'Jeszcze niezaimplementowane.';
	}
}