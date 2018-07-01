<?php

/**
* WizyTówka 5
* Admin page — pages/drafts.
*/
namespace WizyTowka\AdminPages;
use WizyTowka as WT;

class Pages extends WT\AdminPanelPage
{
	use PageUserPermissionCommon;

	protected $_pageTitle = 'Strony';
	protected $_userRequiredPermissions = WT\User::PERM_MANAGE_PAGES;

	private $_pages;
	private $_draftsMode = false;

	private $_settings;

	protected function _prepare()
	{
		$this->_settings = WT\WT()->settings;

		$this->_draftsMode = isset($_GET['drafts']); // If true, show drafts instead public pages.

		if (!$this->_draftsMode and !empty($_GET['hideId'])) {
			$this->_setPageIsDraft($_GET['hideId'], true);
		}
		elseif ($this->_draftsMode and !empty($_GET['publishId'])) {
			$this->_setPageIsDraft($_GET['publishId'], false);
		}
		elseif (!empty($_GET['deleteId'])) {
			$this->_deletePage($_GET['deleteId']);
		}

		$this->_pages = $this->_draftsMode ? WT\Page::getAllDrafts() : WT\Page::getAll();
	}

	private function _setPageIsDraft($pageId, $isDraft)
	{
		// Important: page selected as website homepage must not be moved to drafts.

		if ($page = WT\Page::getById($pageId) and $page->isDraft != $isDraft) {
			if (!$this->_isUserAllowedToPublishOrHidePage($page)) {
				$this->_HTMLMessage->error(
					$page->isDraft ? 'Nie jesteś uprawniony do publikacji tego szkicu.'
					               : 'Nie jesteś uprawniony do ukrywania tej strony.'
				);
			}
			elseif ($page->id == $this->_settings->websiteHomepageId) {
				$this->_HTMLMessage->error(
					'Strona „%s” jest stroną główną witryny. Nie ukryto strony.',
					WT\HTML::correctTypography($page->title)
				);
			}
			else {
				$page->isDraft = $isDraft;
				$page->save();
				$this->_HTMLMessage->success(
					$page->isDraft ? 'Strona „%s” została przeniesiona do szkiców.'
					               : 'Szkic strony „%s” został opublikowany.',
					WT\HTML::correctTypography($page->title)
				);
			}
		}
	}

	private function _deletePage($pageId)
	{
		// Important: page selected as website homepage must not be deleted.

		if ($page = WT\Page::getById($pageId)) {
			if (!$this->_isUserAllowedToEditPage($page)) {
				$this->_HTMLMessage->error(
					$page->isDraft ? 'Nie jesteś uprawniony do usunięcia tego szkicu.'
					               : 'Nie jesteś uprawniony do usunięcia tej strony.'
				);
			}
			elseif ($page->id == $this->_settings->websiteHomepageId) {
				$this->_HTMLMessage->error(
					'Strona „%s” jest stroną główną witryny. Nie usunięto strony.',
					WT\HTML::correctTypography($page->title)
				);
			}
			else {
				$page->delete();
				$this->_HTMLMessage->success(
					$page->isDraft ? 'Szkic strony „%s” został usunięty.'
					               : 'Strona „%s” została usunięta.',
					WT\HTML::correctTypography($page->title)
				);
			}
		}
	}

	protected function _output()
	{
		if (isset($_GET['msg'])) {
			$this->_HTMLMessage->success(
				$this->_draftsMode ? 'Szkic strony został utworzony.' : 'Strona została utworzona.'
			);
		}

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