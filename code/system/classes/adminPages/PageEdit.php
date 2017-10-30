<?php

/**
* WizyTówka 5
* Admin page — page editor (uses content type API).
*/
namespace WizyTowka\AdminPages;
use WizyTowka as WT;

class PageEdit extends WT\AdminPanel
{
	protected $_pageTitle = 'Edycja strony';

	private $_page;
	private $_pageBoxes;
	private $_contentTypeAPI;

	protected function _prepare()
	{
		if (empty($_GET['id']) or !$this->_page = WT\Page::getById($_GET['id'])) {
			$this->_redirect('error', ['type' => 'parameters']);
		}
		$this->_pageBoxes = WT\PageBox::getAll($this->_page->id);

		if (!$contentType = WT\ContentType::getByName($this->_pageBoxes[0]->contentType)) {
			throw PageEditException::contentTypeNotExists($this->_pageBoxes[0]->contentType);
		}
		$this->_contentTypeAPI = $contentType->initEditorPage();
		$this->_contentTypeAPI->setPageData($this->_pageBoxes[0]->contents, $this->_pageBoxes[0]->settings);
		$this->_contentTypeAPI->setHTMLParts($this->_HTMLTemplate, $this->_HTMLHead, $this->_HTMLMessage);
	}

	public function POSTQuery()
	{
		$this->_contentTypeAPI->POSTQuery();

		// Save changes of $contents and $settings property made by content type class.
		// We don't need to assign values because these properties contain instances of \stdClass.
		$this->_pageBoxes[0]->save();

		$this->_HTMLMessage->default('Zmiany zostały zapisane.');
	}

	protected function _output()
	{
		$this->_HTMLContextMenu->add('Edycja', self::URL('pageEdit', ['id' => $this->_page->id]), 'iconEdit');
		$this->_HTMLContextMenu->add('Ustawienia', self::URL('pageSettings', ['id' => $this->_page->id]), 'iconSettings');

		$this->_contentTypeAPI->HTMLContent();
	}
}

class PageEditException extends WT\Exception
{
	static public function contentTypeNotExists($name)
	{
		return new self('Content type "' . $name . '" does not exists.', 1);
	}
}