<?php

/**
* WizyTówka 5
* Admin page — page editor (uses content type API).
*/
namespace WizyTowka\AdminPages;
use WizyTowka as __;

class PageEdit extends __\AdminPanelPage
{
	use PageEditSettingsCommon;

	protected $_pageTitle = 'Edycja strony';
	protected $_userRequiredPermissions = __\User::PERM_MANAGE_PAGES;
}

class PageEditException extends __\Exception
{
	static public function contentTypeNotExists($name)
	{
		return new self('Content type "' . $name . '" does not exists.', 1);
	}
}