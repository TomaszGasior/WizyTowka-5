<?php

/**
* WizyTówka 5
* Admin page — drafts.
*/
namespace WizyTowka\AdminPages;
use WizyTowka as WT;

class Drafts extends WT\AdminPanel
{
	use PageUserPermissionCommon;

	protected $_pageTitle = 'Szkice stron';

	private $_drafts;

	protected function _prepare()
	{
		if (!empty($_GET['publishId'])) {
			$this->_publishDraft($_GET['publishId']);
		}
		elseif (!empty($_GET['deleteId'])) {
			$this->_deleteDraft($_GET['deleteId']);
		}

		$this->_drafts = WT\Page::getAllDrafts();
	}

	private function _publishDraft($pageId)
	{
		if ($page = WT\Page::getById($_GET['publishId'])) {
			if (!$this->_isUserAllowedToEditPage($page)) {
				$this->_HTMLMessage->error('Nie jesteś uprawniony do przenoszenia tego szkicu.');
			}
			else {
				$page->isDraft = false;
				$page->save();
				$this->_HTMLMessage->success('Szkic strony „' . $page->title . '” został opublikowany.');
			}
		}
	}

	private function _deleteDraft($pageId)
	{
		if ($page = WT\Page::getById($_GET['deleteId'])) {
			if (!$this->_isUserAllowedToEditPage($page)) {
				$this->_HTMLMessage->error('Nie jesteś uprawniony do usunięcia tego szkicu.');
			}
			else {
				$page->delete();
				$this->_HTMLMessage->success('Szkic strony „' . $page->title . '” został usunięty.');
			}
		}
	}

	protected function _output()
	{
		if (!empty($_GET['msg'])) {
			$this->_HTMLMessage->success('Szkic strony został utworzony.');
		}

		$this->_HTMLTemplate->drafts = $this->_drafts;
	}
}