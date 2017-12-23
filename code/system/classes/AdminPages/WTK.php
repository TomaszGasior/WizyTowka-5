<?php

/**
* WizyTówka 5
* Admin page — system configuration editor.
*/
namespace WizyTowka\AdminPages;
use WizyTowka as WT;

class WTK extends WT\AdminPanelPage
{
	protected $_pageTitle = 'Wyjątkowo Trudna Konfiguracja';
	protected $_userRequiredPermissions = WT\User::PERM_SUPER_USER;

	private $_settings;
	private $_settingsDefault;

	protected function _prepare()
	{
		$this->_settings        = WT\Settings::get();
		$this->_settingsDefault = WT\Settings::getDefault();
	}

	public function POSTQuery()
	{
		foreach ($this->_settings as $name => $value) {
			if (isset($_POST[$name])) {
				$this->_settings->$name = $_POST[$name];
			}
		}

		$this->_HTMLMessage->success('Zmiany konfiguracji zostały zapisane.');
	}

	protected function _output()
	{
		$this->_HTMLTemplate->settings        = $this->_settings;
		$this->_HTMLTemplate->defaultSettings = $this->_settingsDefault;
	}
}