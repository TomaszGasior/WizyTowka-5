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
	private $_pageBoxes;
	private $_contentTypeAPI;

	protected function _prepare()
	{
		if (empty($_GET['id']) or !$this->_page = WT\Page::getById($_GET['id'])) {
			$this->_redirect('error', ['type' => 'parameters']);
		}
		if (!$this->_isUserAllowedToEditPage($this->_page)) {
			return;
		}

		$this->_pageBoxes = WT\PageBox::getAll($this->_page->id);

		if (!$contentType = WT\ContentType::getByName($this->_pageBoxes[0]->contentType)) {
			$exceptionClass = self::class . 'Exception';
			throw $exceptionClass::contentTypeNotExists($this->_pageBoxes[0]->contentType);
		}
		$this->_contentTypeAPI = $this->_settingsInsteadEdit ? $contentType->initSettingsPage() : $contentType->initEditorPage();
		$this->_contentTypeAPI->setPageData($this->_pageBoxes[0]->contents, $this->_pageBoxes[0]->settings);
		$this->_contentTypeAPI->setHTMLParts($this->_HTMLTemplate, $this->_HTMLHead, $this->_HTMLMessage);
	}

	public function POSTQuery()
	{
		// Redirect user to error page if he is not allowed to edit page.
		$this->_preventFromAccessIfNotAllowed($this->_page);

		$this->_contentTypeAPI->POSTQuery();

		// Save changes of $contents and $settings property made by content type class.
		// We don't need to assign values because these properties contain instances of \stdClass.
		$this->_pageBoxes[0]->save();

		// Save also page to update $updatedTime field in database.
		$this->_page->save();

		$this->_HTMLMessage->default('Zmiany zostały zapisane.');
	}

	protected function _output()
	{
		$this->_settingsInsteadEdit
		? $this->_HTMLContextMenu->add('Edycja', self::URL('pageEdit', ['id' => $this->_page->id]), 'iconEdit')
		: $this->_HTMLContextMenu->add('Ustawienia', self::URL('pageSettings', ['id' => $this->_page->id]), 'iconSettings');
		$this->_HTMLContextMenu->add('Właściwości', self::URL('pageProperties', ['id' => $this->_page->id]), 'iconProperties');

		if (!$this->_isUserAllowedToEditPage($this->_page)) {
			// Show warning if user isn't permitted to modify page.
			$this->_HTMLTemplate->setTemplate('PageEditSettings');
			return;
		}

		$this->_contentTypeAPI->HTMLContent();
	}
}