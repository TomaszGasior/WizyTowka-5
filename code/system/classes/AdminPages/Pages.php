<?php

/**
* WizyTówka 5
* Admin page — pages.
*/
namespace WizyTowka\AdminPages;
use WizyTowka as WT;

class Pages extends WT\AdminPanelPage
{
	use PageUserPermissionCommon;

	protected $_pageTitle = 'Strony';
	protected $_userRequiredPermissions = WT\User::PERM_CREATE_PAGES;

	private $_pages;

	protected function _prepare()
	{
		if (!empty($_GET['hideId'])) {
			$this->_hidePage($_GET['hideId']);
		}
		elseif (!empty($_GET['deleteId'])) {
			$this->_deletePage($_GET['deleteId']);
		}

		$this->_pages = WT\Page::getAll();
	}

	private function _hidePage($pageId)
	{
		// Important: page selected as website homepage must not be moved to drafts.

		if ($page = WT\Page::getById($pageId)) {
			if (!$this->_isUserAllowedToEditPage($page)) {
				$this->_HTMLMessage->error('Nie jesteś uprawniony do przenoszenia tej strony.');
			}
			elseif ($page->id == WT\Settings::get('websiteHomepageId')) {
				$this->_HTMLMessage->error('Wybrana do ukrycia strona jest stroną główną witryny. Nie ukryto strony.');
			}
			else {
				$page->isDraft = true;
				$page->save();
				$this->_HTMLMessage->success('Strona „' . $page->title . '” została przeniesiona do szkiców.');
			}
		}
	}

	private function _deletePage($pageId)
	{
		// Important: page selected as website homepage must not be deleted.

		if ($page = WT\Page::getById($pageId)) {
			if (!$this->_isUserAllowedToEditPage($page)) {
				$this->_HTMLMessage->error('Nie jesteś uprawniony do usunięcia tej strony.');
			}
			elseif ($page->id == WT\Settings::get('websiteHomepageId')) {
				$this->_HTMLMessage->error('Wybrana do usunięcia strona jest stroną główną witryny. Nie usunięto strony.');
			}
			else {
				$page->delete();
				$this->_HTMLMessage->success('Strona „' . $page->title . '” została usunięta.');
			}
		}
	}

	protected function _output()
	{
		if (!empty($_GET['msg'])) {
			$this->_HTMLMessage->success('Strona została utworzona.');
		}

		$this->_HTMLTemplate->pages = $this->_pages;
	}
}