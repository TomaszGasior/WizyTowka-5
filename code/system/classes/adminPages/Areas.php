<?php

/**
* WizyTówka 5
* Admin page — areas.
*/
namespace WizyTowka\AdminPages;
use WizyTowka as WT;

class Areas extends WT\AdminPanel
{
	protected $_pageTitle = 'Obszary';
	protected $_userRequiredPermissions = WT\User::PERM_EDITING_SITE_ELEMENTS;

	protected function _output()
	{
		$this->_apTemplate->setTemplate('Message');

		$this->_apTemplate->CSSClasses = 'iInformations';
		$this->_apTemplate->messageText = 'Jeszcze niezaimplementowane.';
	}
}