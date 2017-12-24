<?php

/**
* WizyTówka 5
* Admin page — customization.
*/
namespace WizyTowka\AdminPages;
use WizyTowka as WT;

class Customization extends WT\AdminPanelPage
{
	protected $_pageTitle = 'Personalizacja';
	protected $_userRequiredPermissions = WT\User::PERM_EDITING_SITE_CONFIG;

	protected function _output()
	{
		$this->_HTMLTemplate->setTemplate('Message');

		$this->_HTMLTemplate->CSSClasses = 'iconInformation';
		$this->_HTMLTemplate->messageText = 'Jeszcze niezaimplementowane.';
	}
}