<?php

/**
* WizyTówka 5
* Admin page — page settings (uses content type API).
*/
namespace WizyTowka\AdminPages;
use WizyTowka as WT;

class PageSettings extends WT\AdminPanelPage
{
	use PageEditSettingsCommon;
	private $_settingsInsteadEdit = true;

	protected $_pageTitle = 'Ustawienia strony';
	protected $_userRequiredPermissions = WT\User::PERM_CREATING_PAGES;
}

class PageSettingsException extends WT\Exception
{
	static public function contentTypeNotExists($name)
	{
		return new self('Content type "' . $name . '" does not exists.', 1);
	}
}