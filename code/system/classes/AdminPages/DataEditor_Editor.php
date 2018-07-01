<?php

/**
* WizyTówka 5
* Admin page — data editor, main part.
*/
namespace WizyTowka\AdminPages;
use WizyTowka as WT;

class DataEditor_Editor extends WT\AdminPanelPage
{
	protected $_pageTitle = 'Edytor plików';
	protected $_userRequiredPermissions = WT\User::PERM_SUPER_USER;

	private $_settings;

	protected function _prepare()
	{
		$this->_settings = WT\WT()->settings;

		if ($this->_settings->lockdownDataEditor) {
			$this->_redirect('error', ['type' => 'lockdown']);
		}
	}

	protected function _output()
	{
		$this->_HTMLTemplate->setTemplate('Message');

		$this->_HTMLTemplate->CSSClasses = 'iconInformation';
		$this->_HTMLTemplate->messageText = 'Jeszcze niezaimplementowane.';
	}
}