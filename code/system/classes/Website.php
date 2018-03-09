<?php

/**
* WizyTówka 5
* Website controller (uses content type API).
*/
namespace WizyTowka;

class Website extends Controller
{
	private $_page;
	private $_contentTypeAPI;

	private $_HTMLTemplate;
	private $_renderer;

	public function __construct()
	{
		// Get current page. If isn't specified, use home page.
		$this->_page = !empty($_GET['id']) ? Page::getBySlug($_GET['id']) : Page::getById(Settings::get('websiteHomepageId'));

		if (!$this->_page or $this->_page->isDraft) {
			die('404'); // Comming soon.
		}

		// Initialize HTML template.
		$this->_HTMLTemplate = new HTMLTemplate;

		// Initialize content type.
		if (!$contentType = ContentType::getByName($this->_page->contentType)) {
			throw WebsiteException::contentTypeNotExists($this->_page->contentType);
		}
		$this->_contentTypeAPI = $contentType->initWebsitePageBox();
		$this->_contentTypeAPI->setPageData($this->_page->contents, $this->_page->settings);

		// Initialize website renderer, which will prepare HTML template.
		$this->_renderer = new WebsiteRenderer($this->_page, $this->_contentTypeAPI, $this->_HTMLTemplate);
	}

	public function POSTQuery()
	{
		try {
			$this->_contentTypeAPI->POSTQuery();
			// ContentTypeAPI::POSTQuery() throws an exception if content type does not support POST queries.
			// Normally it's good, here this behavior is unwanted.
		} catch (ContentTypeAPIException $e) {}
	}

	public function output()
	{
		// ContentTypeAPI::HTMLContent() must be called before template preparing by WebsiteRenderer.
		$this->_contentTypeAPI->HTMLContent();

		$this->_renderer->prepareTemplate();

		$this->_HTMLTemplate->render();
	}

	static public function URL($target, array $arguments = [])
	{
		if (is_integer($target)) {
			if (!$page = Page::getById($target)) {
				return false;
			}
			$slug = $page->slug;
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

		return (Settings::get('websiteAddressRelative') ? '' : Settings::get('websiteAddress'))
		       . ($pretty ? '/' . $slug : '/')
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