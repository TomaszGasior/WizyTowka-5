<?php

/**
* WizyTówka 5
* Admin page — menus.
*/
namespace WizyTowka\AdminPages;
use WizyTowka as WT;

class Menus extends WT\AdminPanel
{
	protected $_pageTitle = 'Menu';
	protected $_userRequiredPermissions = WT\User::PERM_EDITING_SITE_ELEMENTS;

	protected function _output()
	{
		$this->_apTemplate->setTemplate('Message');

		$this->_apTemplate->CSSClasses = 'iInformations';
		$this->_apTemplate->messageText = 'Jeszcze niezaimplementowane.';
	}
}