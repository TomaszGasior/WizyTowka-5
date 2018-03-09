<?php

/**
* WizyTówka 5
* Class that contains set of functions used to prepare template variables and render website HTML code.
*/
namespace WizyTowka;

class WebsiteRenderer
{
	private $_page;

	private $_HTMLLayout;
	private $_HTMLTemplate;
	private $_HTMLHead;
	private $_HTMLMessage;

	private $_theme;

	public function __construct(Page $page, ContentTypeAPI $contentTypeAPI, HTMLTemplate $HTMLLayout)
	{
		$this->_page = $page;

		// Load theme.
		if (!$this->_theme = Theme::getByName(Settings::get('themeName'))) {
			throw WebsiteRendererException::themeNotExists(Settings::get('themeName'));
		}

		// Prepare HTML layout template.
		$this->_HTMLLayout = $HTMLLayout;
		$this->_HTMLLayout->setTemplate('WebsiteLayout');
		$this->_setupTemplatePath($this->_HTMLLayout);

		// Prepare HTML <head> section.
		$this->_HTMLHead = $this->_prepareHead();

		// Initialize HTML message box.
		$this->_HTMLMessage = new HTMLMessage('wt_message');

		// Initialize HTML template for content type.
		$this->_HTMLTemplate = new HTMLTemplate;
		$contentTypeAPI->setHTMLParts($this->_HTMLTemplate, $this->_HTMLHead, $this->_HTMLMessage);
	}

	public function prepareTemplate()
	{
		$layout = $this->_HTMLLayout;

		$layout->lang = Settings::get('websiteLanguage');
		$layout->head = $this->_HTMLHead;

		$layout->setRaw('websiteHeader', $this->_variable_websiteHeader());
		$layout->setRaw('websiteFooter', $this->_variable_websiteFooter());

		$layout->setRaw('pageHeader',  $this->_variable_pageHeader());
		$layout->setRaw('pageContent', $this->_variable_pageContent());

		$layout->setRaw('menu', function(...$a){ return $this->_function_menu(...$a); });
		$layout->setRaw('area', function(...$a){ return $this->_function_area(...$a); });
		$layout->setRaw('info', function(...$a){ return $this->_function_info(...$a); });

		// Change HTML <head> assets path to theme path. Thanks to this adding assets from theme layout will be
		// more convenient. Assets path must be changed at the end of this method because other assets path
		// is set by ContentTypeAPI class for content types purposes.
		$this->_HTMLHead->setAssetsPath($this->_theme->getURL());
	}

	private function _setupTemplatePath(HTMLTemplate $template)
	{
		// Themes can override HTML templates of website layout if it's specified in addon.conf.
		$template->setTemplatePath(
			in_array($template->getTemplate(), $this->_theme->templates) ? $this->_theme->getPath() : SYSTEM_DIR
			. '/templates'
		);
	}

	private function _prepareHead()
	{
		$head = new HTMLHead;

		if (!Settings::get('websiteAddressRelative')) {
			$head->setAssetsPathBase(Settings::get('websiteAddress'));
		}
		$head->setAssetsPath($this->_theme->getURL());

		// Base website information.
		$head->setTitlePattern(HTML::correctTypography(Settings::get('websiteTitlePattern')));
		$head->title(HTML::correctTypography(
			$this->_page->titleHead ? $this->_page->titleHead : $this->_page->title
		));
		if (Settings::get('websiteAuthor')) {
			$head->meta('author', HTML::correctTypography(Settings::get('websiteAuthor')));
		}

		// Search engines information.
		if ($description = $this->_page->description ? $this->_page->description : Settings::get('searchEnginesDescription')) {
			$head->meta('description', HTML::correctTypography($description));
		}
		if ($robots = Settings::get('searchEnginesRobots') or $this->_page->noIndex) {
			// "noindex" option can be set per page or globally for website.
			if ($this->_page->noIndex and strpos($robots, 'noindex') === false) {
				$robots = 'noindex' . ($robots ? ', '.$robots : '');
			}
			$head->meta('robots', $robots);
		}

		// Theme stylesheet.
		$head->stylesheet($this->_theme->minified ? 'style.min.css' : 'style.css');
		if ($this->_theme->responsive) {
			$head->meta('viewport', 'width=device-width, initial-scale=1');
		}

		// DO NOT REMOVE THIS LINE.
		$head->meta('generator', 'WizyTówka CMS — https://wizytowka.tomaszgasior.pl');

		return $head;
	}

	private function _variable_websiteHeader()
	{
		$template = new HTMLTemplate('WebsiteHeader');
		$this->_setupTemplatePath($template);

		$template->websiteTitle       = HTML::correctTypography(Settings::get('websiteTitle'));
		$template->websiteDescription = HTML::correctTypography(Settings::get('websiteDescription'));

		return (string)$template;
	}

	private function _variable_websiteFooter()
	{
		$template = new HTMLTemplate('WebsiteFooter');
		$this->_setupTemplatePath($template);

		$elements = [
			0   => '&copy; ' . HTML::correctTypography(
				Settings::get('websiteAuthor') ? Settings::get('websiteAuthor') : Settings::get('websiteTitle')
			),

			// DO NOT REMOVE THIS LINE.
			999 => '<a href="https://wizytowka.tomaszgasior.pl" title="Ta witryna jest oparta na systemie zarządzania treścią WizyTówka." target="_blank">WizyTówka</a>',
		];

		ksort($elements);
		$template->setRaw('elements', $elements);

		return (string)$template;
	}

	private function _variable_pageHeader()
	{
		$template = new HTMLTemplate('WebsitePageHeader');
		$this->_setupTemplatePath($template);

		$template->pageTitle = HTML::correctTypography($this->_page->title);

		$properties = [];
		if ($user = User::getById($this->_page->userId) and !Settings::get('lockdownUsers')) {
			$properties['Autor'] = HTML::correctTypography($user->name);
		}
		$properties['Data utworzenia']  = HTML::formatDateTime($this->_page->createdTime);
		$properties['Data modyfikacji'] = HTML::formatDateTime($this->_page->updatedTime);

		$template->setRaw('properties', $properties);

		return (string)$template;
	}

	private function _variable_pageContent()
	{
		$template = new HTMLTemplate('WebsitePageContent');
		$this->_setupTemplatePath($template);

		$template->message = HTML::correctTypography($this->_HTMLMessage);
		$template->setRaw('content', $this->_HTMLTemplate);

		return (string)$template;
	}


	private function _function_menu($menuPositionNumber)
	{
		// More comming soon.
		$pages = Page::getAll();
		$menu  = new HTMLMenu;

		foreach ($pages as $page) {
			$menu->append(
				HTML::escape(HTML::correctTypography($page->title)),
				Website::URL($page->slug), $page->slug
			);
		}

		return (string)$menu;
	}

	private function _function_area($areaPositionNumber)
	{
		return '<!-- area ' . $areaPositionNumber . ' comming soon -->';
	}

	private function _function_info($option)
	{
		switch ($option) {
			case 'websiteTitle':       return HTML::correctTypography(Settings::get('websiteTitle'));
			case 'websiteDescription': return HTML::correctTypography(Settings::get('websiteDescription'));
			case 'websiteAuthor':      return HTML::correctTypography(Settings::get('websiteAuthor'));
			case 'pageTitle':          return HTML::correctTypography($this->_page->title);
			case 'pageIsDraft':        return $this->_page->isDraft;
			case 'pageContentType':    return $this->_page->contentType;
			case 'systemVersion':      return VERSION;
		}
	}
}

class WebsiteRendererException
{
	static public function themeNotExists($name)
	{
		return self('Theme "' . $name . '" does not exists.', 1);
	}
}