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
		echo 'Wkrótce…';
	}

	static public function URL($target, array $arguments = [])
	{
		$slug = $target;

		if (is_integer($target)) {
			if ($page = Page::getById($target)) {
				$slug = $page->slug;
			}
			else {
				return false;
			}
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