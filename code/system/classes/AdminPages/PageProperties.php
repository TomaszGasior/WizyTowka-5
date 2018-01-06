<?php

/**
* WizyTówka 5
* Admin page — page properties.
*/
namespace WizyTowka\AdminPages;
use WizyTowka as WT;

class PageProperties extends WT\AdminPanelPage
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
			$newSlug = (new WT\Text(
				!empty($_POST['slug']) ? $_POST['slug'] : $_POST['title'])
			)->makeSlug()->get();

			if (WT\Page::getBySlug($newSlug)) {
				$this->_HTMLMessage->error('Identyfikator „' . $newSlug . '” jest już wykorzystany w innej stronie.');
			}
			else {
				$this->_page->slug = $newSlug;
			}
		}

		$this->_page->titleHead   = trim($_POST['titleHead']);
		$this->_page->isDraft     = (bool)$_POST['isDraft'];
		$this->_page->description = str_replace("\n", ' ', $_POST['description']);
		$this->_page->noIndex     = isset($_POST['noIndex']);

		if ($this->_currentUser->permissions & WT\User::PERM_SUPER_USER) {
			$this->_page->userId = $_POST['userId'];
		}

		$this->_page->save();
		$this->_HTMLMessage->default('Zmiany zostały zapisane.');
	}

	protected function _output()
	{
		$this->_HTMLContextMenu->add('Edycja', self::URL('pageEdit', ['id' => $this->_page->id]), 'iconEdit');
		$this->_HTMLContextMenu->add('Ustawienia', self::URL('pageSettings', ['id' => $this->_page->id]), 'iconSettings');

		$this->_HTMLTemplate->page = $this->_page;

		$usersIdList = array_column(WT\User::getAll(), 'name', 'id');
		if (empty($this->_page->userId)) {
			// userId column is set to NULL by foreign key of DBMS when user is deleted.
			$usersIdList += ['' => '(użytkownik został usunięty)'];
		}
		$this->_HTMLTemplate->usersIdList = $usersIdList;

		$this->_HTMLTemplate->hideUserIdChange    = WT\Settings::get('lockdownUsers');
		$this->_HTMLTemplate->disableUserIdChange = !($this->_currentUser->permissions & WT\User::PERM_SUPER_USER);
		$this->_HTMLTemplate->disableSaveButton   = !$this->_isUserAllowedToEditPage($this->_page);

		// Show warning if user isn't permitted to modify page.
		$this->_HTMLTemplate->permissionLimitNotification = !$this->_isUserAllowedToEditPage($this->_page);
	}
}