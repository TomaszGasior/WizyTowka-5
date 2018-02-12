<?php

/**
* WizyTówka 5
* Admin page — create page/draft.
*/
namespace WizyTowka\AdminPages;
use WizyTowka as WT;

class PageCreate extends WT\AdminPanelPage
{
	protected $_pageTitle = 'Utwórz stronę';
	protected $_userRequiredPermissions = WT\User::PERM_CREATE_PAGES;

	public function POSTQuery()
	{
		$_POST['title'] = trim($_POST['title']);
		$_POST['slug']  = trim($_POST['slug']);

		if (!$_POST['title']) {
			$this->_HTMLMessage->error('Nie określono tytułu strony.');
			return;
		}

		$slug = (new WT\Text(
			!empty($_POST['slug']) ? $_POST['slug'] : $_POST['title'])
		)->makeSlug()->get();

		if (WT\Page::getBySlug($slug)) {
			$this->_HTMLMessage->error('Identyfikator „' . $slug . '” jest już przypisany innej stronie.');
			return;
		}

		if (!$_POST['type']) {
			$this->_HTMLMessage->error('Nie określono typu zawartości strony.');
			return;
		}
		if (!$contentType = WT\ContentType::getByName($_POST['type'])) {
			throw PageCreateException::contentTypeNotExists($_POST['type']);
		}

		$page = new WT\Page;
		$page->title   = $_POST['title'];
		$page->slug    = $slug;
		$page->noIndex = false;
		$page->isDraft = (bool)$_POST['isDraft'];
		$page->userId  = $this->_currentUser->id;
		$page->save();

		try {
			$pageBox = new WT\PageBox;
			$pageBox->pageId         = $page->id;
			$pageBox->contentType    = $_POST['type'];
			$pageBox->settings       = (object)$contentType->settings;
			$pageBox->contents       = (object)$contentType->contents;
			$pageBox->positionRow    = 1;
			$pageBox->positionColumn = 1;
			$pageBox->save();
		} catch (\Throwable $e) {
			$page->delete();   // Delete incomplete created page from database.
			throw $e;
		} catch (\Exception $e) {  // PHP 5.6 backwards compatibility.
			$page->delete();
			throw $e;
		}

		$this->_redirect($page->isDraft ? 'drafts' : 'pages', ['msg' => 1]);
	}

	protected function _output()
	{
		$this->_HTMLTemplate->contentTypes         = WT\ContentType::getAll();
		$this->_HTMLTemplate->autocheckContentType = WT\Settings::get('adminPanelDefaultContentType');
		$this->_HTMLTemplate->autocheckDraft       = isset($_GET['draft']);
	}
}

class PageCreateException extends WT\Exception
{
	static public function contentTypeNotExists($name)
	{
		return new self('Content type "' . $name . '" does not exists.', 1);
	}
}