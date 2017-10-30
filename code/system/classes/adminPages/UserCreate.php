<?php

/**
* WizyTówka 5
* Admin page — create user.
*/
namespace WizyTowka\AdminPages;
use WizyTowka as WT;

class UserCreate extends WT\AdminPanel
{
	protected $_pageTitle = 'Utwórz użytkownika';
	protected $_userRequiredPermissions = WT\User::PERM_SUPER_USER;

	protected function _output()
	{
		$this->_HTMLTemplate->setTemplate('Message');

		$this->_HTMLTemplate->CSSClasses = 'iconInformation';
		$this->_HTMLTemplate->messageText = 'Jeszcze niezaimplementowane.';
	}
}