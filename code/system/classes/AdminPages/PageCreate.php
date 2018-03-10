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
			$this->_HTMLMessage->error('Identyfikator „%s” jest już przypisany innej stronie.', $slug);
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
		$page->userId  = $this->_currentUser->id;

		$page->contentType = $_POST['type'];
		$page->settings    = (object)$contentType->settings;
		$page->contents    = (object)$contentType->contents;

		$page->isDraft = true;
		if ($this->_currentUser->permissions & WT\User::PERM_PUBLISH_PAGES) {
			$page->isDraft = (bool)$_POST['isDraft'];
		}

		$page->save();

		if (WT\Settings::get('adminPanelEditAfterCreate')) {
			$this->_redirect('pageEdit', ['id' => $page->id]);
		}
		else {
			$this->_redirect('pages', $page->isDraft ? ['drafts' => true, 'msg' => 1] : ['msg' => 1]);
		}
	}

	protected function _output()
	{
		$contentTypes = [];
		foreach (WT\ContentType::getAll() as $contentType) {
			$contentTypes[] = (object)[
				'label' => $contentType->label,
				'name'  => $contentType->getName(),
			];
		}
		$this->_HTMLTemplate->contentTypes = $contentTypes;

		$this->_HTMLTemplate->autocheckContentType = WT\Settings::get('adminPanelDefaultContentType');

		$this->_HTMLTemplate->autocheckDraft     = true;
		$this->_HTMLTemplate->disallowPublicPage = true;

		if ($this->_currentUser->permissions & WT\User::PERM_PUBLISH_PAGES) {
			$this->_HTMLTemplate->autocheckDraft     = isset($_GET['draft']);
			$this->_HTMLTemplate->disallowPublicPage = false;
		}
	}
}

class PageCreateException extends WT\Exception
{
	static public function contentTypeNotExists($name)
	{
		return new self('Content type "' . $name . '" does not exists.', 1);
	}
}