<?php

/**
* WizyTówka 5
* Admin page — data editor, file list.
*/
namespace WizyTowka\AdminPages;
use WizyTowka as __;

class DataEditor_List extends __\AdminPanelPage
{
	protected $_pageTitle = 'Edytor plików';
	protected $_userRequiredPermissions = __\User::PERM_SUPER_USER;

	private $_settings;

	protected function _prepare() : void
	{
		$this->_settings = __\WT()->settings;

		if ($this->_settings->lockdownDataEditor) {
			$this->_redirect('error', ['type' => 'lockdown']);
		}
	}

	protected function _output() : void
	{
		$this->_HTMLTemplate->setTemplate('Message');

		$this->_HTMLTemplate->CSSClasses = 'iconInformation';
		$this->_HTMLTemplate->messageText = 'Jeszcze niezaimplementowane.';
	}
}