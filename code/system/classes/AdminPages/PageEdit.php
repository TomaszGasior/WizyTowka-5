<?php

/**
* WizyTówka 5
* Admin page — page editor (uses content type API).
*/
namespace WizyTowka\AdminPages;
use WizyTowka as WT;

class PageEdit extends WT\AdminPanelPage
{
	use PageEditSettingsCommon;
	private $_settingsInsteadEdit = false;

	protected $_pageTitle = 'Edycja strony';
	protected $_userRequiredPermissions = WT\User::PERM_CREATING_PAGES;
}

class PageEditException extends WT\Exception
{
	static public function contentTypeNotExists($name)
	{
		return new self('Content type "' . $name . '" does not exists.', 1);
	}
}