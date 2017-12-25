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
			if (is_bool($value)) {
				$this->_settings->$name = isset($_POST[$name]);
			}
			elseif (isset($_POST[$name])) {
				$this->_settings->$name = $_POST[$name];
			}
		}

		$this->_HTMLMessage->success('Zmiany konfiguracji zostały zapisane.');
	}

	protected function _output()
	{
		$defaults = $this->_settingsDefault;
		$fields   = new WT\HTMLFormFields;

		$prepareArrayFields = function($settings, $group = null) use (&$prepareArrayFields, $fields, $defaults)
		{
			foreach ($settings as $name => $value) {
				$name = $group ? ($group.'['.$name.']') : $name;

				if (is_array($value)) {
					$prepareArrayFields($value, $name);
				}
				else {
					$attributes = (empty($group) and isset($defaults->$name))
					              ? ['title' => 'Domyślna wartość tego ustawienia: „'.$defaults->$name.'”.'] : [];
					$fields->{is_bool($value) ? 'checkbox' : 'text'}($name, $name, $value, $attributes);
				}
			}
		};

		$prepareArrayFields($this->_settings);

		$this->_HTMLTemplate->fields = $fields;
	}
}