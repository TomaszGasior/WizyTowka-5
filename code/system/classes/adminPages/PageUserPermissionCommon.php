<?php

/**
* WizyTówka 5
* Common code for PageEdit, PageSettings, PageProperties, Pages, Drafts controllers.
*/
namespace WizyTowka\AdminPages;
use WizyTowka as WT;

trait PageUserPermissionCommon
{
	// Return true if user is allowed to edit $page, otherwise false.
	// User can edit page if he is owner of it or if he have permission to edit pages owned by others.
	private function _isUserAllowedToEditPage(WT\Page $page)
	{
		if ($this->_currentUser->permissions & WT\User::PERM_EDITING_OTHERS_PAGES
			or $this->_currentUser->id == $page->userId) {
			return true;
		}

		return false;
	}

	// Prevent unauthorized access.
	private function _preventFromAccessIfNotAllowed(WT\Page $page)
	{
		if (!$this->_isUserAllowedToEditPage($page)) {
			self::_redirect('error', ['type' => 'permissions']);
		}
	}
}