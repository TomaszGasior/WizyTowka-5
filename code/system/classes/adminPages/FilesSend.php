<?php

/**
* WizyTówka 5
* Admin page — send file(s).
*/
namespace WizyTowka\AdminPages;
use WizyTowka as WT;

class FilesSend extends WT\AdminPanel
{
	protected $_pageTitle = 'Wyślij pliki';
	protected $_userRequiredPermissions = WT\User::PERM_SENDING_FILES;

	protected function _output()
	{
		$this->_HTMLTemplate->setTemplate('Message');

		$this->_HTMLTemplate->CSSClasses = 'iconInformation';
		$this->_HTMLTemplate->messageText = 'Jeszcze niezaimplementowane.';
	}
}