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

		if (empty($_POST['type'])) {
			$this->_apMessage->error('Nie określono typu zawartości strony.');
			return;
		}
		$contentType = WT\ContentType::getByName($_POST['type']);

		$page = new WT\Page;
		$page->title   = $_POST['title'];
		$page->slug    = $slug;
		$page->isDraft = (bool)$_POST['isDraft'];
		$page->userId  = $this->_currentUser->id;
		$page->save();

		try {
			$pageBox = new WT\PageBox;
			$pageBox->pageId         = $page->id;
			$pageBox->type           = $_POST['type'];
			$pageBox->settings       = (object)(isset($contentType->settings) ? $contentType->settings : []);
			$pageBox->contents       = (object)(isset($contentType->contents) ? $contentType->contents : []);
			$pageBox->positionRow    = 1;
			$pageBox->positionColumn = 1;
			$pageBox->save();
		} catch (\Throwable $e) {
			$page->delete();   // Delete incomplete created page from database.
			throw $e;
		}

		$this->_redirect($page->isDraft ? 'drafts' : 'pages', ['msg' => 1]);
	}

	protected function _output()
	{
		$this->_apTemplate->contentTypes         = WT\ContentType::getAll();
		$this->_apTemplate->autocheckContentType = WT\Settings::get('adminPanelDefaultContentType');
		$this->_apTemplate->autocheckDraft       = !empty($_GET['draft']);
	}
}