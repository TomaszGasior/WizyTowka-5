<?php

/**
* WizyTówka 5
* Admin page — backup manager.
*/
namespace WizyTowka\AdminPages;
use WizyTowka as WT;

class Backup extends WT\AdminPanel
{
	protected $_pageTitle = 'Kopia zapasowa';
	protected $_userRequiredPermissions = WT\User::PERM_SUPER_USER;

	protected function _output()
	{
		$this->_HTMLTemplate->setTemplate('Message');

		$this->_HTMLTemplate->CSSClasses = 'iconInformation';
		$this->_HTMLTemplate->messageText = 'Jeszcze niezaimplementowane.';
	}
}