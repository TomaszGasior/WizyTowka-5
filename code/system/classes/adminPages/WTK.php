<?php

/**
* WizyTówka 5
* Admin page — system configuration editor.
*/
namespace WizyTowka\AdminPages;
use WizyTowka as WT;

class WTK extends WT\AdminPanel
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

		$this->_apMessage->success('Zmiany konfiguracji zostały zapisane.');
	}

	protected function _output()
	{
		$this->_apTemplate->settings        = $this->_settings;
		$this->_apTemplate->defaultSettings = $this->_settingsDefault;
	}
}