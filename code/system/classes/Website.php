<?php

/**
* WizyTówka 5
* Website controller.
*/
namespace WizyTowka;

class Website extends Controller
{
	public function output()
	{
		echo 'Wkrótce… ';
	}

	static public function URL($target, $arguments = [])
	{
		$slug = $target;
		if (is_numeric($target) and $page = Page::getById($target)) {
			$slug = $page->slug;
		}

		if (isset($arguments['id'])) {
			throw ControllerException::unallowedKeyInURLArgument('id');
		}
		if (!$pretty = Settings::get('websitePrettyLinks')) {
			$arguments['id'] = $slug;
		}

		return Settings::get('websiteAddress') . ($pretty ? '/'.$slug : '/') . ($arguments ? '?'.http_build_query($arguments) : '');
	}
}