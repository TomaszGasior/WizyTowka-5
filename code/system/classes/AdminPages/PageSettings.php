<?php

/**
* WizyTówka 5
* Admin page — page settings (uses content type API).
*/
namespace WizyTowka\AdminPages;
use WizyTowka as __;

class PageSettings extends __\AdminPanelPage
{
	use PageEditSettingsCommon;

	protected $_pageTitle = 'Ustawienia strony';
	protected $_userRequiredPermissions = __\User::PERM_MANAGE_PAGES;
}

class PageSettingsException extends __\Exception
{
	static public function contentTypeNotExists($name)
	{
		return new self('Content type "' . $name . '" does not exists.', 1);
	}
}