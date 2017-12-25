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
	private $_HTMLBoxes = [];
	private $_HTMLHead;
	private $_HTMLMessage;

	private $_theme;

	public function __construct(Page $page, array $contentTypeAPIBoxes, HTMLTemplate $HTMLLayout)
	{
		$this->_page = $page;

		if (!$this->_theme = Theme::getByName(Settings::get('themeName'))) {
			throw WebsiteRendererException::themeNotExists(Settings::get('themeName'));
		}

		$this->_HTMLLayout = $HTMLLayout;
		$this->_HTMLLayout->setTemplate('WebsiteLayout');
		$this->_setupTemplatePath($this->_HTMLLayout);

		$this->_HTMLHead = $this->_prepareHead();

		$this->_HTMLMessage = new HTMLMessage;

		foreach ($contentTypeAPIBoxes as $box) {
			$HTMLBox = new HTMLTemplate;

			$this->_HTMLBoxes[] = $HTMLBox;
			$box->setHTMLParts($HTMLBox, $this->_HTMLHead, $this->_HTMLMessage);
		}
	}

	private function _setupTemplatePath(HTMLTemplate $template)
	{
		$template->setTemplatePath(
			(
				(isset($this->_theme->templates) and isset($this->_theme->templates[$template->getTemplate()]))
				? $this->_theme->getPath() : SYSTEM_DIR
			)
			. '/templates'
		);
	}

	private function _prepareHead()
	{
		$head = new HTMLHead;
		$head->setAssetsPathBase(Settings::get('websiteAddress'));
		$head->setAssetsPath($this->_theme->getURL());

		$head->title(HTML::correctTypography(sprintf(Settings::get('websiteTitle'), $this->_page->title)));
		$head->stylesheet('style.css');
		$head->meta('Generator', 'WizyTówka CMS — https://wizytowka.tomaszgasior.pl');

		return $head;
	}

	public function prepareTemplate()
	{
		$layout = $this->_HTMLLayout;

		$layout->lang = Settings::get('websiteLanguage');
		$layout->head = $this->_HTMLHead;

		$layout->websiteHeader = $this->_variable_websiteHeader();
		$layout->websiteFooter = $this->_variable_websiteFooter();

		$layout->pageHeader  = $this->_variable_pageHeader();
		$layout->pageContent = $this->_variable_pageContent();

		$layout->menu = function(...$a) { return $this->_function_menu(...$a); };
		$layout->area = function(...$a) { return $this->_function_area(...$a); };
		$layout->info = function(...$a) { return $this->_function_info(...$a); };

		// Change HTML <head> assets path to theme path. Thanks to this adding assets from theme layout will be
		// more convenient. Assets path must be changed at the end of this method because other assets path
		// is set by ContentTypeAPI class for content types purposes.
		$this->_HTMLHead->setAssetsPath($this->_theme->getURL());
	}

	private function _variable_websiteHeader()
	{
		$template = new HTMLTemplate('WebsiteHeader');
		$this->_setupTemplatePath($template);

		$template->websiteTitle       = HTML::correctTypography(Settings::get('websiteTitle'));
		$template->websiteDescription = HTML::correctTypography(Settings::get('websiteDescription'));

		ob_start();
		$template->render();
		return ob_get_clean();
	}

	private function _variable_websiteFooter()
	{
		$template = new HTMLTemplate('WebsiteFooter');
		$this->_setupTemplatePath($template);

		$elements = [
			0   => '&copy; ' . HTML::correctTypography(Settings::get('websiteAuthor')),
			999 => '<a href="https://wizytowka.tomaszgasior.pl" title="Ta witryna jest oparta na systemie zarządzania treścią WizyTówka.">WizyTówka</a>',
		];

		ksort($elements);
		$template->elements = $elements;

		ob_start();
		$template->render();
		return ob_get_clean();
	}

	private function _variable_pageHeader()
	{
		$template = new HTMLTemplate('WebsitePageHeader');
		$this->_setupTemplatePath($template);

		$template->pageTitle = HTML::correctTypography($this->_page->title);

		$properties = [];
		if ($user = User::getById($this->_page->userId)) {
			$properties['Autor'] = HTML::correctTypography($user->name);
		}
		$properties['Data utworzenia']  = HTML::formatDateTime($this->_page->createdTime);
		$properties['Data modyfikacji'] = HTML::formatDateTime($this->_page->updatedTime);

		$template->properties = $properties;

		ob_start();
		$template->render();
		return ob_get_clean();
	}

	private function _variable_pageContent()
	{
		$template = new HTMLTemplate('WebsitePageContent');
		$this->_setupTemplatePath($template);

		$template->message   = HTML::correctTypography($this->_HTMLMessage);
		$template->pageBoxes = $this->_HTMLBoxes;

		ob_start();
		$template->render();
		return ob_get_clean();
	}


	private function _function_menu($menuPositionNumber)
	{
		// More comming soon.
		$pages = Page::getAll();
		$menu  = new HTMLMenu;

		foreach ($pages as $page) {
			$menu->add(HTML::correctTypography($page->title), Website::URL($page->id), $page->slug);
		}

		ob_start();
		$menu->output();
		return ob_get_clean();
	}

	private function _function_area($areaPositionNumber)
	{
		// More comming soon.
		return '';
	}

	private function _function_info($option)
	{
		switch ($option) {
			case 'websiteTitle':       return Settings::get('websiteTitle');
			case 'websiteDescription': return Settings::get('websiteDescription');
			case 'pageTitle':          return $this->_page->title;
			case 'version':            return VERSION;
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