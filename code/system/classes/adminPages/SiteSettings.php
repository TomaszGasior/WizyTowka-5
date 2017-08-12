<?php

/**
* WizyTówka 5
* Admin page — site settings.
*/
namespace WizyTowka\AdminPages;
use WizyTowka as WT;

class SiteSettings extends WT\AdminPanel
{
	protected $_pageTitle = 'Ustawienia witryny';
	protected $_userRequiredPermissions = WT\User::PERM_EDITING_SITE_CONFIG;

	private $_settings;

	protected function _prepare()
	{
		$this->_settings = WT\Settings::get();
	}

	public function POSTQuery()
	{
		if (empty($_POST['websiteTitle']) or empty($_POST['websiteTitlePattern']) or empty($_POST['websiteAddress'])
			or empty($_POST['websiteEmailAddress']) or empty($_POST['websiteHomepageId'])) {
			$this->_apMessageError = true;
			$this->_apMessage = 'Nie wypełniono wymaganych pól.';
			return;
		}

		// Try to update ".htaccess" file, when pretty links setting was changed.
		// Give information about problem to user, when it is not possible.
		if ($this->_settings->websitePrettyLinks != isset($_POST['websitePrettyLinks'])
			and !$this->_updateHtaccess()) {
			$this->_apMessageError = true;
			$this->_apMessage = 'Zmiany zostały zapisane. Przyjazne odnośniki wymagają ręcznej konfiguracji serwera.';
		}

		$this->_settings->websiteTitle        = $_POST['websiteTitle'];
		$this->_settings->websiteAuthor       = $_POST['websiteAuthor'];
		$this->_settings->websiteTitlePattern = $_POST['websiteTitlePattern'];
		$this->_settings->websiteAddress      = $_POST['websiteAddress'];
		$this->_settings->websiteHomepageId   = $_POST['websiteHomepageId'];

		$this->_settings->websiteEmailAddress = $_POST['websiteEmailAddress'];
		$this->_settings->websiteDateFormat   = $_POST['websiteDateFormat'];
		$this->_settings->websitePrettyLinks  = isset($_POST['websitePrettyLinks']);

		// Title pattern must have place for page title "%s".
		if (mb_strpos($this->_settings->websiteTitlePattern, '%s') === false) {
			$this->_settings->websiteTitlePattern = '%s — ' . $this->_settings->websiteTitlePattern;
		}

		if (!$this->_apMessage) {
			$this->_apMessage = 'Zmiany zostały zapisane.';
		}
	}

	protected function _output()
	{
		$pages = WT\Page::getAll();
		$this->_apTemplate->pagesIds = array_combine(array_column($pages, 'id'), array_column($pages, 'title'));

		$this->_apTemplate->dateFormats = [
			'Jeszcze niezaimplementowane',
		];

		$this->_apTemplate->settings = $this->_settings;
	}

	private function _updateHtaccess()
	{
		return false;
	}
}