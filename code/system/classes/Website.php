<?php

/**
* WizyTówka 5
* Website controller (uses content type API).
*/
namespace WizyTowka;

class Website extends Controller
{
	private $_page;
	private $_contentTypeAPIBoxes = [];

	private $_HTMLTemplate;
	private $_renderer;

	public function __construct()
	{
		// Get current page. If isn't specified, use home page.
		$this->_page = !empty($_GET['id']) ? Page::getBySlug($_GET['id']) : Page::getById(Settings::get('websiteHomepageId'));

		if (!$this->_page or $this->_page->isDraft) {
			die('404');
		}

		// Initialize HTML template.
		$this->_HTMLTemplate = new HTMLTemplate;

		// Get page boxes and its contents. Initialize content types.
		foreach (PageBox::getAll($this->_page->id) as $pageBox) {
			if (!$contentType = ContentType::getByName($pageBox->contentType)) {
				throw WebsiteException::contentTypeNotExists($pageBox->contentType);
			}

			$contentTypeAPI = $contentType->initWebsitePageBox();
			$contentTypeAPI->setPageData($pageBox->contents, $pageBox->settings);
			$this->_contentTypeAPIBoxes[] = $contentTypeAPI;
		}

		// Initialize website renderer, which will prepare HTML template.
		$this->_renderer = new WebsiteRenderer($this->_page, $this->_contentTypeAPIBoxes, $this->_HTMLTemplate);
	}

	public function POSTQuery()
	{
		foreach ($this->_contentTypeAPIBoxes as $box) {
			try {
				$box->POSTQuery();
				// ContentTypeAPI::POSTQuery() throws an exception if content type does not support POST queries.
				// Normally it's good, here this behavior is unwanted.
			} catch (ContentTypeAPIException $e) {}
		}
	}

	public function output()
	{
		foreach ($this->_contentTypeAPIBoxes as $box) {
			$box->HTMLContent();
		}

		// ContentTypeAPI::HTMLContent() must be called before template preparing by WebsiteRenderer.
		$this->_renderer->prepareTemplate();

		$this->_HTMLTemplate->render();
	}

	static public function URL($target, array $arguments = [])
	{
		if (is_numeric($target)) {
			if ($page = Page::getById($target)) {
				$slug = $page->slug;
			}
			else {
				return false;
			}
		}
		else {
			$slug = $target;
		}

		if (isset($arguments['id'])) {
			throw ControllerException::unallowedKeyInURLArgument('id');
		}
		if (!$pretty = Settings::get('websitePrettyLinks')) {
			$arguments = ['id' => $slug] + $arguments;   // Adds "id" argument to array beginning for better URL readability.
		}

		return Settings::get('websiteAddress') . ($pretty ? '/' . $slug : '/')
		       . ($arguments ? '?' . http_build_query($arguments) : '');
	}
}

class WebsiteException extends Exception
{
	static public function contentTypeNotExists($name)
	{
		return new self('Content type "' . $name . '" does not exists.', 1);
	}
}