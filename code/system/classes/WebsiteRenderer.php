<?php

/**
* WizyTówka 5
* Class that contains set of functions used to prepare template variables and render website HTML code.
*/
namespace WizyTowka;

class WebsiteRenderer
{
	private $_page;
	private $_template;
	private $_head;
	private $_theme;
	private $_contentType;

	public function _construct(Page $page, HTMLTemplate $template, HTMLHead $head, Theme $theme, ContentType $contentType)
	{
		$this->_page = $page;

		$this->_template = $template;
		$this->_head     = $head;

		$this->_theme       = $theme;
		$this->_contentType = $contentType;
	}

	public function buildVariables()
	{
		$this->_template->head    = $this->_prepareHead();
		$this->_template->title   = $this->_page->title;

		$this->_template->header  = $this->_buildHeader();
		$this->_template->footer  = $this->_buildFooter();
		$this->_template->content = $this->_buildContent();

		$this->_template->menu = function($menuPositionNumber)
		{
			return '';
		};

		$this->_template->area = function($areaPositionNumber)
		{
			return '';
		};

		$this->_template->info = function($option)
		{
			switch ($option) {
				case 'websiteTitle': return Settings::get('websiteTitle');
				case 'pageTitle':    return $this->_page->title;
				case 'version':      return VERSION;
			}
		};
	}

	private function _prepareHead()
	{
		$this->_head->setTitle(sprintf(Settings::get('websiteTitle'), $this->_page->title));

		return $this->_head;
	}

	private function _buildHeader()
	{
		ob_start();

		echo '<h1>', Settings::get('websiteTitle'), '</h1>';

		return ob_get_clean();
	}


	private function _buildFooter()
	{
		ob_start();

		echo '<ul>';
		echo '<li>&copy; ', Settings::get('websiteTitle'), '</li>';
		echo '</ul>';

		return ob_get_clean();
	}

	private function _buildContent()
	{
		ob_start();

		echo 'Example content.';

		return ob_get_clean();
	}
}