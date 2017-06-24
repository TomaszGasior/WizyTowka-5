<?php

/**
* WizyTówka 5
* Admin page — data editor, main part.
*/
namespace WizyTowka\AdminPages;
use WizyTowka as WT;

class DataEditor_Editor extends WT\AdminPanel
{
	protected $_pageTitle = 'Edytor plików';
	protected $_userRequiredPermissions = WT\User::PERM_SUPER_USER;

	protected function _output()
	{
		$this->_apTemplate->setTemplate('Message');

		$this->_apTemplate->CSSClasses = 'iconInformation';
		$this->_apTemplate->messageText = 'Jeszcze niezaimplementowane.';
	}
}