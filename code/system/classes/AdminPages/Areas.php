<?php

/**
* WizyTówka 5
* Admin page — areas.
*/
namespace WizyTowka\AdminPages;
use WizyTowka as WT;

class Areas extends WT\AdminPanelPage
{
	protected $_pageTitle = 'Obszary';
	protected $_userRequiredPermissions = WT\User::PERM_EDITING_SITE_ELEMENTS;

	protected function _output()
	{
		$this->_HTMLTemplate->setTemplate('Message');

		$this->_HTMLTemplate->CSSClasses = 'iconInformation';
		$this->_HTMLTemplate->messageText = 'Jeszcze niezaimplementowane.';
	}
}