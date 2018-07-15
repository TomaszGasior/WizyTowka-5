<?php

/**
* WizyTówka 5
* Admin page — pages/drafts.
*/
namespace WizyTowka\AdminPages;
use WizyTowka as __;

class Pages extends __\AdminPanelPage
{
	use PageUserPermissionCommon;

	protected $_pageTitle = 'Strony';
	protected $_userRequiredPermissions = __\User::PERM_MANAGE_PAGES;

	private $_pages;
	private $_draftsMode = false;

	private $_settings;

	protected function _prepare() : void
	{
		$this->_settings = __\WT()->settings;

		$this->_draftsMode = (bool)($_GET['drafts'] ?? null);  // If true, show drafts instead public pages.

		if (!$this->_draftsMode and is_numeric($_GET['hideId'] ?? null)) {
			$this->_setPageIsDraft((int)$_GET['hideId'], true);
		}
		elseif ($this->_draftsMode and is_numeric($_GET['publishId'] ?? null)) {
			$this->_setPageIsDraft((int)$_GET['publishId'], false);
		}
		elseif (is_numeric($_GET['deleteId'] ?? null)) {
			$this->_deletePage((int)$_GET['deleteId']);
		}

		$this->_pages = $this->_draftsMode ? __\Page::getAllDrafts() : __\Page::getAll();
	}

	private function _setPageIsDraft(int $pageId, bool $isDraft) : void
	{
		// Important: page selected as website homepage must not be moved to drafts.

		if ($page = __\Page::getById($pageId) and $page->isDraft != $isDraft) {
			if (!$this->_isUserAllowedToPublishOrHidePage($page)) {
				$this->_HTMLMessage->error(
					$page->isDraft ? 'Nie jesteś uprawniony do publikacji tego szkicu.'
					               : 'Nie jesteś uprawniony do ukrywania tej strony.'
				);
			}
			elseif ($page->id == $this->_settings->websiteHomepageId) {
				$this->_HTMLMessage->error(
					'Strona „%s” jest stroną główną witryny. Nie ukryto strony.',
					__\HTML::correctTypography($page->title)
				);
			}
			else {
				$page->isDraft = $isDraft;
				$page->save();
				$this->_HTMLMessage->success(
					$page->isDraft ? 'Strona „%s” została przeniesiona do szkiców.'
					               : 'Szkic strony „%s” został opublikowany.',
					__\HTML::correctTypography($page->title)
				);
			}
		}
	}

	private function _deletePage(int $pageId) : void
	{
		// Important: page selected as website homepage must not be deleted.

		if ($page = __\Page::getById($pageId)) {
			if (!$this->_isUserAllowedToEditPage($page)) {
				$this->_HTMLMessage->error(
					$page->isDraft ? 'Nie jesteś uprawniony do usunięcia tego szkicu.'
					               : 'Nie jesteś uprawniony do usunięcia tej strony.'
				);
			}
			elseif ($page->id == $this->_settings->websiteHomepageId) {
				$this->_HTMLMessage->error(
					'Strona „%s” jest stroną główną witryny. Nie usunięto strony.',
					__\HTML::correctTypography($page->title)
				);
			}
			else {
				$page->delete();
				$this->_HTMLMessage->success(
					$page->isDraft ? 'Szkic strony „%s” został usunięty.'
					               : 'Strona „%s” została usunięta.',
					__\HTML::correctTypography($page->title)
				);
			}
		}
	}

	protected function _output() : void
	{
		if ($this->_draftsMode) {
			$this->_pageTitle = 'Szkice stron';
			$this->_HTMLHead->title($this->_pageTitle);
			$this->_HTMLTemplate->setTemplate('PagesDrafts');
		}

		$this->_HTMLContextMenu->append(
			$this->_draftsMode ? 'Utwórz szkic' : 'Utwórz stronę',
			self::URL('pageCreate', $this->_draftsMode ? ['draft' => true] : []),
			'iconAdd'
		);

		$this->_HTMLTemplate->pages = $this->_pages;
	}
}