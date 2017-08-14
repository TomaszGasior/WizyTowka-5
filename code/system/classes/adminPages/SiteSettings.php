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
			$this->_apMessageError = true;
			$this->_apMessage = 'Nie wypełniono wymaganych pól.';
			return;
		}

		// Try to update ".htaccess" file, when pretty links setting was changed.
		// Give information about problem to user if it is not possible.
		if ($this->_settings->websitePrettyLinks != isset($_POST['websitePrettyLinks']) and !$this->_updateHtaccess()) {
			$this->_apMessageError = true;
			$this->_apMessage = 'Zmiany zostały zapisane. Przyjazne odnośniki wymagają ręcznej konfiguracji serwera.';
		}

		$this->_settings->websiteTitle        = $_POST['websiteTitle'];
		$this->_settings->websiteAuthor       = $_POST['websiteAuthor'];
		$this->_settings->websiteTitlePattern = $_POST['websiteTitlePattern'];
		$this->_settings->websiteAddress      = $_POST['websiteAddress'];
		$this->_settings->websiteHomepageId   = $_POST['websiteHomepageId'];

		$this->_settings->websiteEmailAddress = $_POST['websiteEmailAddress'];
		$this->_settings->websitePrettyLinks  = isset($_POST['websitePrettyLinks']);

		// Date/time format can be changed in configuration file. In this case form field will be disabled.
		if (isset($_POST['websiteDateFormat']) and in_array($_POST['websiteDateFormat'], $this->_definedDateFormats)) {
			$this->_settings->websiteDateFormat   = $_POST['websiteDateFormat'];
		}

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
		$this->_apTemplate->settings = $this->_settings;

		// PHP 5.6 backwards compatibility for using objects in array_columns() function.
		// More here: http://php.net/manual/en/function.array-column.php#refsect1-function.array-column-changelog
		$array_column = function($input, $column_key, $index_key){
			if (PHP_VERSION_ID >= 70000) {
				return array_column($input, $column_key, $index_key);
			}
			$array = [];
			foreach ($input as $object) {
				$array[$object->$index_key] = $object->$column_key;
			}
			return $array;
		};

		// "Website homepage" field — titles of public pages.
		$pages = WT\Page::getAll();
		$this->_apTemplate->pagesIds = $array_column($pages, 'title', 'id');

		// "Date/time format" field — list with formats and examples.
		$dateFormatsAndExamples = [];
		foreach ($this->_definedDateFormats as $format) {
			$dateFormatsAndExamples[$format] = (new WT\Text(1472711447))->formatAsDateTime($format)->get();
		}
		$this->_apTemplate->dateFormatsAndExamples = $dateFormatsAndExamples;

		// "Date/time format" field — disable if setting was changed in configuration file.
		$this->_apTemplate->disableDateFormatField = !in_array($this->_settings->websiteDateFormat, $this->_definedDateFormats);
	}

	private function _updateHtaccess()
	{
		return false;
	}
}