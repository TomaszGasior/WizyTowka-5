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
	protected $_userRequiredPermissions = WT\User::PERM_MANAGE_PAGES;

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

		if (!$_POST['title']) {
			$this->_HTMLMessage->error('Nie określono tytułu strony.');
		}
		else {
			$this->_page->title = $_POST['title'];
		}

		if ($this->_page->id == WT\Settings::get('websiteHomepageId') and (bool)$_POST['isDraft']) {
			$this->_HTMLMessage->error('Nie można przenieść do szkiców strony głównej witryny.');
		}
		elseif ($this->_currentUser->permissions & WT\User::PERM_PUBLISH_PAGES) {
			$this->_page->isDraft = (bool)$_POST['isDraft'];
		}

		if ($_POST['slug'] != $this->_page->slug) {
			$newSlug = (new WT\Text(
				!empty($_POST['slug']) ? $_POST['slug'] : $_POST['title'])
			)->makeSlug()->get();

			if (WT\Page::getBySlug($newSlug)) {
				$this->_HTMLMessage->error('Identyfikator „%s” jest już przypisany innej stronie.', $newSlug);
			}
			else {
				$this->_page->slug = $newSlug;
			}
		}

		$this->_page->titleHead   = trim($_POST['titleHead']);
		$this->_page->description = str_replace("\n", ' ', $_POST['description']);
		$this->_page->noIndex     = isset($_POST['noIndex']);

		if ($this->_currentUser->permissions & WT\User::PERM_EDIT_PAGES) {
			$this->_page->userId = $_POST['userId'];
			// We don't need to validate this value. DBMS won't allow inserting invalid ID because of constraints.
		}

		$this->_page->save();
		$this->_HTMLMessage->default('Zmiany zostały zapisane.');
	}

	protected function _output()
	{
		// Replace default admin page title by website page title.
		$this->_pageTitle = WT\HTML::correctTypography($this->_page->title);
		$this->_HTMLHead->title('Właściwości: „' . $this->_pageTitle . '”');

		$this->_HTMLContextMenu->append('Edycja',     self::URL('pageEdit',     ['id' => $this->_page->id]), 'iconEdit');
		$this->_HTMLContextMenu->append('Ustawienia', self::URL('pageSettings', ['id' => $this->_page->id]), 'iconSettings');

		$this->_HTMLTemplate->page = $this->_page;

		// "userId" property.
		$this->_HTMLTemplate->hideUserIdChange = true;
		$this->_HTMLTemplate->usersIdList      = [];

		if (!WT\Settings::get('lockdownUsers')) {
			$this->_HTMLTemplate->hideUserIdChange = false;

			$usersIdList = array_column(WT\User::getAll(), 'name', 'id');
			if (!$this->_page->userId) {
				// userId column is set to NULL by foreign key of DBMS when user is deleted.
				$usersIdList += ['' => '(użytkownik został usunięty)'];
			}
			$this->_HTMLTemplate->usersIdList = $usersIdList;
		}

		// "noIndex" property.
		$this->_HTMLTemplate->disableNoIndex = false;
		if (strpos(WT\Settings::get('searchEnginesRobots'), 'noindex') !== false) {
			$this->_page->noIndex = true; // Fake value used to check the checkbox, won't be saved in database.
			$this->_HTMLTemplate->disableNoIndex = true;
		}

		// Check current user permissions.
		$this->_HTMLTemplate->disallowUserIdChange = !($this->_currentUser->permissions & WT\User::PERM_EDIT_PAGES);
		$this->_HTMLTemplate->disallowPublicPage   = !($this->_currentUser->permissions & WT\User::PERM_PUBLISH_PAGES);

		// Show warning and lock form controls when user isn't permitted to modify page.
		$this->_HTMLTemplate->disallowModifications = !$this->_isUserAllowedToEditPage($this->_page);
	}
}