<?php

/**
* WizyTówka 5
* Admin page — menus.
*/
namespace WizyTowka\AdminPages;
use WizyTowka as __;

class Menus extends __\AdminPanelPage
{
	protected $_pageTitle = 'Menu';
	protected $_userRequiredPermissions = __\User::PERM_WEBSITE_ELEMENTS;

	protected function _output() : void
	{
		$this->_HTMLTemplate->setTemplate('Message');

		$this->_HTMLTemplate->CSSClasses = 'iconInformation';
		$this->_HTMLTemplate->messageText = 'Jeszcze niezaimplementowane.';
	}
}