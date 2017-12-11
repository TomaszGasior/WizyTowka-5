<?php

/**
* WizyTówka 5
* Admin page — page properties.
*/
namespace WizyTowka\AdminPages;
use WizyTowka as WT;

class PageProperties extends WT\AdminPanel
{
	use PageUserPermissionCommon;

	protected $_pageTitle = 'Właściwości strony';
	protected $_userRequiredPermissions = WT\User::PERM_CREATING_PAGES;

	private $_page;

	public function _prepare()
	{
		if (empty($_GET['id']) or !$this->_page = WT\Page::getById($_GET['id'])) {
			$this->_redirect('error', ['type' => 'parameters']);
		}
	}

	public function POSTQuery()
	{
		// Redirect user to error page if he is not allowed to edit page.
		$this->_preventFromAccessIfNotAllowed($this->_page);

		$_POST['title'] = trim($_POST['title']);
		$_POST['slug']  = trim($_POST['slug']);

		if (empty($_POST['title'])) {
			$this->_HTMLMessage->error('Nie określono tytułu strony.');
		}
		else {
			$this->_page->title = $_POST['title'];
		}

		if ($_POST['slug'] != $this->_page->slug) {
			$newSlug = !empty($_POST['slug']) ? $_POST['slug'] : (new WT\Text($_POST['title']))->makeSlug()->get();

			if (WT\Page::getBySlug($newSlug)) {
				$this->_HTMLMessage->error('Identyfikator „' . $newSlug . '” jest już wykorzystany w innej stronie.');
			}
			else {
				$this->_page->slug = $newSlug;
			}
		}

		$this->_page->isDraft     = (bool)$_POST['isDraft'];
		$this->_page->description = $_POST['description'];
		$this->_page->keywords    = $_POST['keywords'];

		$this->_page->save();
		$this->_HTMLMessage->default('Zmiany zostały zapisane.');
	}

	protected function _output()
	{
		$this->_HTMLTemplate->page = $this->_page;

		$users = WT\User::getAll();
		$this->_HTMLTemplate->userIdList           = array_column($users, 'name', 'id');
		$this->_HTMLTemplate->userIdSelected       = $this->_page->id;
		$this->_HTMLTemplate->userIdDisallowChange = !($this->_currentUser->permissions & WT\User::PERM_SUPER_USER);

		$this->_HTMLTemplate->permissionLimitNotification = !$this->_isUserAllowedToEditPage($this->_page);
		$this->_HTMLTemplate->disableSaveButton           = !$this->_isUserAllowedToEditPage($this->_page);
	}
}