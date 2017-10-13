<?php

/**
* WizyTówka 5
* Admin page — drafts.
*/
namespace WizyTowka\AdminPages;
use WizyTowka as WT;

class Drafts extends WT\AdminPanel
{
	protected $_pageTitle = 'Szkice stron';

	private $_drafts;

	protected function _prepare()
	{
		if (!empty($_GET['publishId']) and $page = WT\Page::getById($_GET['publishId'])) {
			$page->isDraft = false;
			$page->save();
			$this->_apMessage->success('Szkic strony „' . $page->title . '” został opublikowany.');
		}
		if (!empty($_GET['deleteId']) and $page = WT\Page::getById($_GET['deleteId'])) {
			$page->delete();
			$this->_apMessage->success('Szkic strony „' . $page->title . '” został usunięty.');
		}

		$this->_drafts = WT\Page::getAllDrafts();
	}

	protected function _output()
	{
		if (!empty($_GET['msg'])) {
			$this->_apMessage->success('Szkic strony został utworzony.');
		}

		$this->_apTemplate->drafts = $this->_drafts;
	}
}