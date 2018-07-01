<?php

/**
* WizyTówka 5
* Admin page — backup manager.
*/
namespace WizyTowka\AdminPages;
use WizyTowka as __;

class Backup extends __\AdminPanelPage
{
	protected $_pageTitle = 'Kopia zapasowa';
	protected $_userRequiredPermissions = __\User::PERM_SUPER_USER;

	private $_settings;

	protected function _prepare()
	{
		$this->_settings = __\WT()->settings;

		if ($this->_settings->lockdownBackup) {
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