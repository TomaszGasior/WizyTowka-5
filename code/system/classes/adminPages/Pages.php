<?php

/**
* WizyTówka 5
* Admin page — pages.
*/
namespace WizyTowka\AdminPages;
use WizyTowka as WT;

class Pages extends WT\AdminPanel
{
	protected $_pageTitle = 'Strony';

	private $_pages;

	protected function _prepare()
	{
		if (!empty($_GET['hideId']) and $page = WT\Page::getById($_GET['hideId'])) {
			$page->isDraft = true;
			$page->save();
			$this->_apMessage = 'Strona „' . $page->title . '” została przeniesiona do szkiców.';
		}
		if (!empty($_GET['deleteId']) and $page = WT\Page::getById($_GET['deleteId'])) {
			$page->delete();
			$this->_apMessage = 'Strona „' . $page->title . '” została usunięta.';
		}

		$this->_pages = WT\Page::getAll();
	}

	protected function _output()
	{
		if (!empty($_GET['msg'])) {
			$this->_apMessage = 'Strona została utworzona.';
		}

		$this->_apTemplate->pages = $this->_pages;
	}
}