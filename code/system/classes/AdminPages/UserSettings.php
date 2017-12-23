<?php

/**
* WizyTówka 5
* Admin page — user settings.
*/
namespace WizyTowka\AdminPages;
use WizyTowka as WT;

class UserSettings extends WT\AdminPanelPage
{
	protected $_pageTitle = 'Ustawienia użytkownika';

	protected function _output()
	{
		$this->_HTMLTemplate->setTemplate('Message');

		$this->_HTMLTemplate->CSSClasses = 'iconInformation';
		$this->_HTMLTemplate->messageText = 'Jeszcze niezaimplementowane.';
	}
}