<?php

/**
* WizyTówka 5
* Admin page — areas.
*/
namespace WizyTowka\AdminPages;
use WizyTowka as __;

class Areas extends __\AdminPanelPage
{
	protected $_pageTitle = 'Obszary';
	protected $_userRequiredPermissions = __\User::PERM_WEBSITE_ELEMENTS;

	protected function _output()
	{
		$this->_HTMLTemplate->setTemplate('Message');

		$this->_HTMLTemplate->CSSClasses = 'iconInformation';
		$this->_HTMLTemplate->messageText = 'Jeszcze niezaimplementowane.';
	}
}