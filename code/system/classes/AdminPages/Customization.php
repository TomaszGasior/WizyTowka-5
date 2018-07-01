<?php

/**
* WizyTówka 5
* Admin page — customization.
*/
namespace WizyTowka\AdminPages;
use WizyTowka as __;

class Customization extends __\AdminPanelPage
{
	protected $_pageTitle = 'Personalizacja';
	protected $_userRequiredPermissions = __\User::PERM_WEBSITE_SETTINGS;

	protected function _output()
	{
		$this->_HTMLTemplate->setTemplate('Message');

		$this->_HTMLTemplate->CSSClasses = 'iconInformation';
		$this->_HTMLTemplate->messageText = 'Jeszcze niezaimplementowane.';
	}
}