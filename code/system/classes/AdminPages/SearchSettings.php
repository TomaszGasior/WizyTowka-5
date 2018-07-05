<?php

/**
* WizyTówka 5
* Admin page — search engines settings settings.
*/
namespace WizyTowka\AdminPages;
use WizyTowka as __;

class SearchSettings extends __\AdminPanelPage
{
	protected $_pageTitle = 'Informacje wyszukiwarek';
	protected $_userRequiredPermissions = __\User::PERM_WEBSITE_SETTINGS;

	private $_robotsSettings = [];

	private $_settings;

	protected function _prepare() : void
	{
		$this->_settings = __\WT()->settings;

		$this->_robotsSettings = array_map('strtolower', array_map('trim',
			explode(',', $this->_settings->searchEnginesRobots)
		));
	}

	public function POSTQuery() : void
	{
		$this->_settings->searchEnginesDescription = str_replace("\n", ' ', $_POST['searchEnginesDescription']);

		foreach (['noindex', 'noimageindex', 'noarchive', 'nofollow'] as $option) {
			if (isset($_POST['robots'][$option])) {
				$this->_robotsSettings[] = $option;
			}
			else {
				$this->_robotsSettings = array_diff($this->_robotsSettings, [$option]);
			}
		}
		$this->_settings->searchEnginesRobots = implode(', ', array_filter(array_unique(
			array_diff($this->_robotsSettings, ['index', 'follow'])
		)));

		$this->_HTMLMessage->default('Zmiany zostały zapisane.');
	}

	protected function _output() : void
	{
		$this->_HTMLTemplate->settings = $this->_settings;

		$this->_HTMLTemplate->robots = [
			'noindex'      => in_array('noindex',      $this->_robotsSettings),
			'noimageindex' => in_array('noimageindex', $this->_robotsSettings),
			'noarchive'    => in_array('noarchive',    $this->_robotsSettings),
			'nofollow'     => in_array('nofollow',     $this->_robotsSettings),
		];
	}
}