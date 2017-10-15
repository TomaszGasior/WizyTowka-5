<?php

/**
* WizyTówka 5
* Website controller.
*/
namespace WizyTowka;

class Website extends Controller
{
	private $_page;

	public function __construct()
	{
		$this->_page = !empty($_GET['id']) ? Page::getBySlug($_GET['id']) : Page::getById(Settings::get('websiteHomepageId'));
	}

	public function output()
	{
		echo $this->_page->title;
	}

	static public function URL($target, array $arguments = [])
	{
		if (is_integer($target)) {
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

		return Settings::get('websiteAddress') . ($pretty ? '/'.$slug : '/') . ($arguments ? '?'.http_build_query($arguments) : '');
	}
}