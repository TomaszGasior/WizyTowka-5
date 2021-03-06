<?php

/**
* WizyTówka 5
* Admin page — system configuration editor.
*/
namespace WizyTowka\AdminPages;
use WizyTowka as __;

class WTK extends __\AdminPanelPage
{
	protected $_pageTitle = 'Wyjątkowo Trudna Konfiguracja';
	protected $_userRequiredPermissions = __\User::PERM_SUPER_USER;

	private $_settings;
	private $_settingsDefault;

	protected function _prepare() : void
	{
		$this->_settings        = __\WT()->settings;
		$this->_settingsDefault = __\WT()->getDefaultSettings();

		if ($this->_settings->lockdownWTK) {
			$this->_redirect('error', ['type' => 'lockdown']);
		}
	}

	public function POSTQuery() : void
	{
		foreach ($this->_settings as $name => $value) {
			if (is_bool($value)) {
				$this->_settings->$name = isset($_POST[$name]);
			}
			elseif (isset($_POST[$name])) {
				$this->_settings->$name = $_POST[$name];
			}
		}

		$this->_HTMLMessage->success('Zmiany konfiguracji zostały zapisane.');
	}

	protected function _output() : void
	{
		$defaults = $this->_settingsDefault;
		$fields   = new __\HTMLFormFields;

		$prepareArrayFields = function($settings, $group = null) use (&$prepareArrayFields, $fields, $defaults)
		{
			foreach ($settings as $name => $value) {
				$name = $group ? ($group.'['.$name.']') : $name;

				if (is_array($value)) {
					$prepareArrayFields($value, $name);
				}
				else {
					$attributes = [];
					if (empty($group) and isset($defaults->$name)) {
						$attributes = ['title' => 'Domyślna wartość: ' .
							(is_bool($defaults->$name) ? ($defaults->$name ? 'zaznaczony' : 'odznaczony') : ('„' . $defaults->$name . '”'))
						. '.'];
					}
					$fields->{is_bool($value) ? 'checkbox' : 'text'}($name, $name, $value, $attributes);
				}
			}
		};

		$prepareArrayFields($this->_settings);

		$this->_HTMLTemplate->fields = $fields;
	}
}