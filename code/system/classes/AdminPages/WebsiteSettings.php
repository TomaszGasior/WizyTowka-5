<?php

/**
* WizyTówka 5
* Admin page — website settings.
*/
namespace WizyTowka\AdminPages;
use WizyTowka as WT;

class WebsiteSettings extends WT\AdminPanelPage
{
	protected $_pageTitle = 'Ustawienia witryny';
	protected $_userRequiredPermissions = WT\User::PERM_EDITING_SITE_CONFIG;

	private $_settings;

	private $_definedDateFormats = [
		'%Y-%m-%d %H:%M:%S',
		'%Y-%m-%d %H:%M',
		'%Y-%m-%d %k:%M',
		'%d.%m.%Y %H:%M',
		'%e.%m.%Y %H:%M',
		'%e.%m.%Y %k:%M',
		'%e %B %Y, %k:%M',
		'%A, %e %B %Y, %k:%M',
		'%e %B %Y, %A, %k:%M',
		'%m/%d/%y %H:%M',
	];

	protected function _prepare()
	{
		$this->_settings = WT\Settings::get();
	}

	public function POSTQuery()
	{
		if (empty($_POST['websiteTitle']) or empty($_POST['websiteTitlePattern']) or empty($_POST['websiteAddress'])
			or empty($_POST['websiteEmailAddress']) or empty($_POST['websiteHomepageId'])) {
			$this->_HTMLMessage->error('Nie wypełniono wymaganych pól.');
			return;
		}

		// Try to update ".htaccess" file, when pretty links setting was changed.
		// Tell user about problem, when he enabled pretty links and server is other than Apache.
		if ($this->_settings->websitePrettyLinks != isset($_POST['websitePrettyLinks'])
			and !$this->_updateHtaccess(isset($_POST['websitePrettyLinks'])) and isset($_POST['websitePrettyLinks'])) {
			$this->_HTMLMessage->error('Zmiany zostały zapisane. Przyjazne odnośniki wymagają ręcznej konfiguracji serwera.');
		}

		// Date/time format can be changed in configuration file. In this case form field will be disabled.
		if (isset($_POST['websiteDateFormat']) and in_array($_POST['websiteDateFormat'], $this->_definedDateFormats)) {
			$this->_settings->websiteDateFormat = $_POST['websiteDateFormat'];
		}

		$this->_settings->websiteTitle        = $_POST['websiteTitle'];
		$this->_settings->websiteAuthor       = $_POST['websiteAuthor'];
		$this->_settings->websiteTitlePattern = $_POST['websiteTitlePattern'];
		$this->_settings->websiteAddress      = $_POST['websiteAddress'];
		$this->_settings->websiteHomepageId   = $_POST['websiteHomepageId'];

		$this->_settings->websiteEmailAddress = $_POST['websiteEmailAddress'];
		$this->_settings->websitePrettyLinks  = isset($_POST['websitePrettyLinks']);

		// Website address should not have "/" at the end.
		if (substr($this->_settings->websiteAddress, -1) == '/') {
			$this->_settings->websiteAddress = substr($this->_settings->websiteAddress, 0, -1);
		}

		// Title pattern must have place for page title "%s".
		if (strpos($this->_settings->websiteTitlePattern, '%s') === false) {
			$this->_settings->websiteTitlePattern = '%s — ' . $this->_settings->websiteTitlePattern;
		}

		$this->_HTMLMessage->default('Zmiany zostały zapisane.');
	}

	private function _updateHtaccess($enablePrettyLinks)
	{
		if (empty($_SERVER['SERVER_SOFTWARE']) or stripos($_SERVER['SERVER_SOFTWARE'], 'Apache') === false) {
			return false;
		}

		try {
			$htaccessContent = file_exists('.htaccess') ? file_get_contents('.htaccess') : '';

			if ($enablePrettyLinks) {
				$websiteAddressPath = ($p = parse_url($this->_settings->websiteAddress, PHP_URL_PATH)) ? $p : '/';
				$htaccessRule = <<< HTACCESS
# WizyTowka
RewriteEngine on
RewriteBase $websiteAddressPath
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule /?([A-Za-z0-9-._]+)/?$ index.php?id=$1 [QSA,L]
# WizyTowka
HTACCESS;
				$htaccessContent .= "\n\n\n" . $htaccessRule;
			}
			else {
				$htaccessContent = preg_replace('/# WizyTowka.*# WizyTowka/s', null, $htaccessContent);
			}

			$htaccessContent = trim($htaccessContent);
			$htaccessContent ? file_put_contents('.htaccess', $htaccessContent) : @unlink('.htaccess');
		}
		catch (\ErrorException $e) {
			WT\ErrorHandler::addToLog($e);
			return false;
		}

		return true;
	}

	protected function _output()
	{
		$this->_HTMLTemplate->settings = $this->_settings;

		// "Website homepage" field — titles of public pages.
		$pages = WT\Page::getAll();
		$this->_HTMLTemplate->pagesIds = array_column($pages, 'title', 'id');

		// "Date/time format" field — list with formats and examples.
		$dateFormatsAndExamples = [];
		foreach ($this->_definedDateFormats as $format) {
			$dateFormatsAndExamples[$format] = (new WT\Text(1472711447))->formatAsDateTime($format)->get();
		}
		$this->_HTMLTemplate->dateFormatsAndExamples = $dateFormatsAndExamples;

		// "Date/time format" field — disable if setting was changed in configuration file.
		$this->_HTMLTemplate->disableDateFormatField = !in_array($this->_settings->websiteDateFormat, $this->_definedDateFormats);
	}
}