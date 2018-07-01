<?php

/**
* WizyTówka 5
* Website controller (uses content type API).
*/
namespace WizyTowka;

class Website extends Controller
{
	private $_page = null;   // 404 error if it's null.
	private $_contentTypeAPI = null;

	private $_HTMLTemplate;
	private $_renderer;

	private $_settings;

	public function __construct()
	{
		$this->_settings = WT()->settings;

		// Get current page. If isn't specified, use home page.
		$this->_page = !empty($_GET['id']) ? Page::getBySlug($_GET['id'])
		               : Page::getById($this->_settings->websiteHomepageId);

		// There is 404 error if page doesn't exist or if page is hidden (it's a draft).
		if (!$this->_page or $this->_page->isDraft) {
			$this->_page = null;
		}

		$this->_sendHTTPHeaders();

		// Initialize HTML template.
		$this->_HTMLTemplate = new HTMLTemplate;

		// Initialize content type.
		if ($this->_page) {
			if (!$contentType = ContentType::getByName($this->_page->contentType)) {
				throw WebsiteException::contentTypeNotExists($this->_page->contentType);
			}
			$this->_contentTypeAPI = $contentType->initWebsitePageBox();
			$this->_contentTypeAPI->setPageData($this->_page->contents, $this->_page->settings);
		}

		// Initialize website renderer, which will prepare HTML template.
		$this->_renderer = new WebsiteRenderer($this->_HTMLTemplate, $this->_page, $this->_contentTypeAPI);
	}

	private function _sendHTTPHeaders()
	{
		// 404 error header.
		if (!$this->_page) {
			header('HTTP/1.1 404 Not Found');
		}

		// Better security.
		header('X-XSS-Protection: 1; mode=block');  // Works in MSIE and WebKit/Blink.
		header('X-Content-Type-Options: nosniff');
		header('X-Frame-Options: Deny');

		// DO NOT REMOVE THIS LINE.
		header('X-Powered-By: WizyTowka CMS');
	}

	public function POSTQuery()
	{
		if ($this->_page) {
			try {
				$this->_contentTypeAPI->POSTQuery();
				// ContentTypeAPI::POSTQuery() throws an exception if content type does not support POST queries.
				// Normally it's good, here this behavior is unwanted.
			} catch (ContentTypeAPIException $e) {}
		}
	}

	public function output()
	{
		// ContentTypeAPI::HTMLContent() must be called before WebsiteRenderer::prepareTemplate().
		if ($this->_page) {
			$this->_contentTypeAPI->HTMLContent();
		}

		$this->_renderer->prepareTemplate();

		$this->_HTMLTemplate->render();
	}

	static public function URL($target, array $arguments = [])
	{
		$settings = WT()->settings;

		$slug = (string)$target;

		if (is_integer($target)) {
			if (!$page = Page::getById($target)) {
				return null;
			}

			$slug = $page->slug;

			// Don't append slug to absolute page URL if it's current home page.
			if ($page->id == $settings->websiteHomepageId and !$settings->websiteAddressRelative) {
				$slug = '';
			}
		}

		$prettyLinks = $settings->websitePrettyLinks;

		if (isset($arguments['id'])) {
			throw ControllerException::unallowedKeyInURLArgument('id');
		}
		if (!$prettyLinks and $slug) {
			$arguments = ['id' => $slug] + $arguments;   // Adds "id" argument to array beginning for better URL readability.
		}

		return ($settings->websiteAddressRelative ? '' : $settings->websiteAddress . '/')
		       . (($prettyLinks and $slug) ? '/' . $slug : '')
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