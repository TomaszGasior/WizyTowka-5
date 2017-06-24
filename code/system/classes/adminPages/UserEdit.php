<?php

/**
* WizyTówka 5
* Admin page — user editor.
*/
namespace WizyTowka\AdminPages;
use WizyTowka as WT;

class UserEdit extends WT\AdminPanel
{
	protected $_pageTitle = 'Edycja użytkownika';
	protected $_userRequiredPermissions = WT\User::PERM_SUPER_USER;

	protected function _output()
	{
		$this->_apTemplate->setTemplate('Message');

		$this->_apTemplate->CSSClasses = 'iconInformation';
		$this->_apTemplate->messageText = 'Jeszcze niezaimplementowane.';
	}
}