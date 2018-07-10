<?php

/**
* WizyTówka 5
* Admin page — create page/draft.
*/
namespace WizyTowka\AdminPages;
use WizyTowka as __;

class PageCreate extends __\AdminPanelPage
{
	protected $_pageTitle = 'Utwórz stronę';
	protected $_userRequiredPermissions = __\User::PERM_CREATE_PAGES;

	private $_settings;

	protected function _prepare() : void
	{
		$this->_settings = __\WT()->settings;
	}

	public function POSTQuery() : void
	{
		$_POST['title'] = trim($_POST['title']);
		$_POST['slug']  = trim($_POST['slug']);

		if (!$_POST['title']) {
			$this->_HTMLMessage->error('Nie określono tytułu strony.');
			return;
		}

		$slug = (new __\Text(
			!empty($_POST['slug']) ? $_POST['slug'] : $_POST['title'])
		)->makeSlug()->get();

		if (__\Page::getBySlug($slug)) {
			$this->_HTMLMessage->error('Identyfikator „%s” jest już przypisany innej stronie.', $slug);
			return;
		}

		if (!$_POST['type']) {
			$this->_HTMLMessage->error('Nie określono typu zawartości strony.');
			return;
		}
		if (!$contentType = __\ContentType::getByName($_POST['type'])) {
			throw PageCreateException::contentTypeNotExists($_POST['type']);
		}

		$page = new __\Page;

		$page->title   = $_POST['title'];
		$page->slug    = $slug;
		$page->noIndex = false;
		$page->userId  = $this->_currentUser->id;

		$page->contentType = $_POST['type'];
		$page->settings    = (object)$contentType->settings;
		$page->contents    = (object)$contentType->contents;

		$page->isDraft = true;
		if ($this->_currentUser->permissions & __\User::PERM_PUBLISH_PAGES) {
			$page->isDraft = (bool)$_POST['isDraft'];
		}

		$page->save();

		$this->_HTMLMessage->success(
			$page->isDraft ? 'Szkic strony został utworzony.' : 'Strona została utworzona.'
		);

		$this->_settings->adminPanelEditAfterCreate
		? $this->_redirect('pageEdit', ['id' => $page->id])
		: $this->_redirect('pages',    ['drafts' => $page->isDraft]);
	}

	protected function _output() : void
	{
		$contentTypes = [];
		foreach (__\ContentType::getAll() as $contentType) {
			$contentTypes[] = (object)[
				'label' => $contentType->label,
				'name'  => $contentType->getName(),
			];
		}
		$this->_HTMLTemplate->contentTypes = $contentTypes;

		$this->_HTMLTemplate->autocheckContentType = $this->_settings->adminPanelDefaultContentType;

		$this->_HTMLTemplate->autocheckDraft     = true;
		$this->_HTMLTemplate->disallowPublicPage = true;

		if ($this->_currentUser->permissions & __\User::PERM_PUBLISH_PAGES) {
			$this->_HTMLTemplate->autocheckDraft     = isset($_GET['draft']);
			$this->_HTMLTemplate->disallowPublicPage = false;
		}
	}
}

class PageCreateException extends __\Exception
{
	static public function contentTypeNotExists($name)
	{
		return new self('Content type "' . $name . '" does not exists.', 1);
	}
}