<?php

/**
* WizyTówka 5
* Admin page — menus.
*/
namespace WizyTowka\AdminPages;
use WizyTowka as WT;

class Menus extends WT\AdminPanelPage
{
	protected $_pageTitle = 'Menu';
	protected $_userRequiredPermissions = WT\User::PERM_EDITING_SITE_ELEMENTS;

	protected function _output()
	{
		$this->_HTMLTemplate->setTemplate('Message');

		$this->_HTMLTemplate->CSSClasses = 'iconInformation';
		$this->_HTMLTemplate->messageText = 'Jeszcze niezaimplementowane.';
	}
}