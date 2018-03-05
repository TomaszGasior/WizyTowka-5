<?php

/**
* WizyTówka 5
* Common code for PageEdit and PageSettings controllers (uses content type API).
*/
namespace WizyTowka\AdminPages;
use WizyTowka as WT;

trait PageEditSettingsCommon
{
	use PageUserPermissionCommon;

	private $_page;
	private $_contentTypeAPI;

	private $_settingsMode; // True for PageSettings, false for PageEdit class.

	protected function _prepare()
	{
		$this->_settingsMode = (self::class == __NAMESPACE__ . '\PageSettings');

		if (empty($_GET['id']) or !$this->_page = WT\Page::getById($_GET['id'])) {
			$this->_redirect('error', ['type' => 'parameters']);
		}

		// Don't redirect here to permissions error page. Instead show nice error message inside _output().
		if (!$this->_isUserAllowedToEditPage($this->_page)) {
			return;
		}

		if (!$contentType = WT\ContentType::getByName($this->_page->contentType)) {
			$exceptionClass = self::class . 'Exception';  // Syntax for backwards compatibility with PHP 5.6.
			throw $exceptionClass::contentTypeNotExists($this->_page->contentType);
		}
		$this->_contentTypeAPI = $this->_settingsMode ? $contentType->initSettingsPage() : $contentType->initEditorPage();
		$this->_contentTypeAPI->setPageData($this->_page->contents, $this->_page->settings);
		$this->_contentTypeAPI->setHTMLParts($this->_HTMLTemplate, $this->_HTMLHead, $this->_HTMLMessage);
	}

	public function POSTQuery()
	{
		// Redirect user to error page if he is not allowed to edit page.
		$this->_preventFromAccessIfNotAllowed($this->_page);

		$this->_contentTypeAPI->POSTQuery();

		// Save changes of $contents and $settings property made by content type class.
		// We don't need to assign values because these properties contain instances of \stdClass.
		// $updatedTime field in database is updated here.
		$this->_page->save();

		$this->_HTMLMessage->default('Zmiany zostały zapisane.');
	}

	protected function _output()
	{
		// Replace default admin page title by website page title.
		$this->_pageTitle = WT\HTML::correctTypography($this->_page->title);
		$this->_HTMLHead->title(
			($this->_settingsMode ? 'Ustawienia' : 'Edycja') .  ': „' . $this->_pageTitle . '”'
		);

		// Context menu. Show "Edit" link on "Settings" admin page and "Settings" link on "Edit" page.
		$this->_settingsMode
		? $this->_HTMLContextMenu->append('Edycja',     self::URL('pageEdit',       ['id' => $this->_page->id]), 'iconEdit')
		: $this->_HTMLContextMenu->append('Ustawienia', self::URL('pageSettings',   ['id' => $this->_page->id]), 'iconSettings');
		$this->_HTMLContextMenu->append('Właściwości',  self::URL('pageProperties', ['id' => $this->_page->id]), 'iconProperties');

		// Show warning if user isn't permitted to modify page.
		if (!$this->_isUserAllowedToEditPage($this->_page)) {
			$this->_HTMLTemplate->setTemplate('PageEditSettings');
			return;
		}

		$this->_contentTypeAPI->HTMLContent();
	}
}