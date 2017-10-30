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
		// Important: page selected as website homepage should not be deleted or moved to drafts.

		if (!empty($_GET['hideId']) and $page = WT\Page::getById($_GET['hideId'])) {
			if ($page->id == WT\Settings::get('websiteHomepageId')) {
				$this->_HTMLMessage->error('Wybrana do ukrycia strona jest stroną główną witryny. Nie ukryto strony.');
			}
			else {
				$page->isDraft = true;
				$page->save();
				$this->_HTMLMessage->success('Strona „' . $page->title . '” została przeniesiona do szkiców.');
			}
		}
		if (!empty($_GET['deleteId']) and $page = WT\Page::getById($_GET['deleteId'])) {
			if ($page->id == WT\Settings::get('websiteHomepageId')) {
				$this->_HTMLMessage->error('Wybrana do usunięcia strona jest stroną główną witryny. Nie usunięto strony.');
			}
			else {
				$page->delete();
				$this->_HTMLMessage->success('Strona „' . $page->title . '” została usunięta.');
			}
		}

		$this->_pages = WT\Page::getAll();
	}

	protected function _output()
	{
		if (!empty($_GET['msg'])) {
			$this->_HTMLMessage->success('Strona została utworzona.');
		}

		$this->_HTMLTemplate->pages = $this->_pages;
	}
}