<?php

/**
* WizyTówka 5
* Admin page — create page/draft.
*/
namespace WizyTowka\AdminPages;
use WizyTowka as WT;

class PageCreate extends WT\AdminPanel
{
	protected $_pageTitle = 'Utwórz stronę';
	protected $_userRequiredPermissions = WT\User::PERM_CREATING_PAGES;

	public function POSTQuery()
	{
		$_POST['title'] = trim($_POST['title']);
		$_POST['slug']  = trim($_POST['slug']);

		if (empty($_POST['title'])) {
			$this->_apMessage->error('Nie określono tytułu strony.');
			return;
		}

		$slug = (new WT\Text(
			!empty($_POST['slug']) ? $_POST['slug'] : $_POST['title'])
		)->makeSlug()->get();

		if (WT\Page::getBySlug($slug)) {
			$this->_apMessage->error('Identyfikator „' . $slug . '” jest już wykorzystany w innej stronie.');
			return;
		}

		$page = new WT\Page;

		$page->title   = $_POST['title'];
		$page->slug    = $slug;
		$page->isDraft = (bool)$_POST['isDraft'];
		$page->userId  = $this->_currentUser->id;

		$page->save();

		$this->_redirect($page->isDraft ? 'drafts' : 'pages', ['msg' => 1]);
	}

	protected function _output()
	{
		$this->_apTemplate->autocheckDraft = !empty($_GET['draft']);

		$this->_apTemplate->boxesTypes = [];  // Comming soon.
		$this->_apTemplate->autocheckBoxType = '';  // Comming soon.
	}
}