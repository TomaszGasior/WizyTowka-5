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
	protected $_userRequiredPermissions = WT\User::PERM_WEBSITE_SETTINGS;

	private $_settings;

	private $_dateTimeFormatCurrent  = '';
	private $_dateTimeFormatDisable  = false;
	private $_dateTimeDefinedFormats = [
		// Order: date format, separator, time format.
		['%Y-%m-%d'    , ' ' , '%H:%M:%S'],
		['%Y-%m-%d'    , ' ' , '%H:%M'   ],
		['%Y-%m-%d'    , ' ' , '%k:%M'   ],
		['%d.%m.%Y'    , ' ' , '%H:%M'   ],
		['%e.%m.%Y'    , ' ' , '%H:%M'   ],
		['%e.%m.%Y'    , ' ' , '%k:%M'   ],
		['%e %B %Y'    , ', ', '%k:%M'   ],
		['%A, %e %B %Y', ', ', '%k:%M'   ],
		['%e %B %Y, %A', ', ', '%k:%M'   ],
		['%m/%d/%y'    , ' ' , '%H:%M'   ],
	];

	protected function _prepare()
	{
		$this->_settings = WT\Settings::get();

		// Disallow modifying of date time format if settings was changed outside GUI.
		$this->_dateTimeFormatCurrent = [$this->_settings->dateDateFormat, $this->_settings->dateSeparator,
		                                 $this->_settings->dateTimeFormat];
		$this->_dateTimeFormatDisable = !in_array($this->_dateTimeFormatCurrent, $this->_dateTimeDefinedFormats);
	}

	public function POSTQuery()
	{
		if (!$_POST['websiteTitle'] or !$_POST['websiteTitlePattern'] or !$_POST['websiteAddress']
			or !$_POST['websiteEmailAddress'] or !$_POST['websiteHomepageId']) {
			$this->_HTMLMessage->error('Nie wypełniono wymaganych pól.');
			return;
		}

		// Try to update ".htaccess" file, when pretty links setting was changed.
		// Tell user about problem, when he enabled pretty links and server is other than Apache.
		if ($this->_settings->websitePrettyLinks != isset($_POST['websitePrettyLinks'])
			and !$this->_updateHtaccess(isset($_POST['websitePrettyLinks']))
			and isset($_POST['websitePrettyLinks'])) {
			$this->_HTMLMessage->error('Zmiany zostały zapisane. Przyjazne odnośniki wymagają ręcznej konfiguracji serwera.');
		}

		// Date/time format can be changed in configuration file. In this case form field will be disabled.
		if (!$this->_dateTimeFormatDisable and isset($_POST['dateTimeFormat'])
			and isset($this->_dateTimeDefinedFormats[$_POST['dateTimeFormat']])) {
			$this->_dateTimeFormatCurrent    = $this->_dateTimeDefinedFormats[$_POST['dateTimeFormat']];
			$this->_settings->dateDateFormat = $this->_dateTimeFormatCurrent[0];
			$this->_settings->dateSeparator  = $this->_dateTimeFormatCurrent[1];
			$this->_settings->dateTimeFormat = $this->_dateTimeFormatCurrent[2];
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

		$this->_settings->typographyQuotes  = isset($_POST['typographyQuotes']);
		$this->_settings->typographyDashes  = isset($_POST['typographyDashes']);
		$this->_settings->typographyOrphans = isset($_POST['typographyOrphans']);
		$this->_settings->typographyOther   = isset($_POST['typographyOther']);

		$this->_HTMLMessage->default('Zmiany zostały zapisane.');
	}

	protected function _output()
	{
		$this->_HTMLContextMenu->append('Informacje wyszukiwarek', self::URL('searchSettings'), 'iconSearch');

		$this->_HTMLTemplate->settings = $this->_settings;

		// "Website homepage" field — titles of public pages.
		$pagesIds = array_column(WT\Page::getAll(), 'title', 'id');
		array_walk($pagesIds, function(&$title){ $title = WT\HTML::correctTypography($title); });
		$this->_HTMLTemplate->pagesIds = $pagesIds;

		// "Date/time format" field — current format and list with formats and examples.
		$dateTimeFormatList = [];
		if ($this->_dateTimeFormatDisable) {
			$dateTimeFormatList[''] = '(format niestandardowy)';
			$dateTimeFormatSelected = '';
		}
		else {
			foreach ($this->_dateTimeDefinedFormats as $key => $format) {
				$dateTimeFormatList[$key] = (new WT\Text(1472741330))->formatAsDateTime(
					join($this->_settings->dateSwapTime ? array_reverse($format) : $format)
				)->get();
			}
			$dateTimeFormatSelected = array_search($this->_dateTimeFormatCurrent, $this->_dateTimeDefinedFormats);
		}
		$this->_HTMLTemplate->dateTimeFormatList     = $dateTimeFormatList;
		$this->_HTMLTemplate->dateTimeFormatSelected = $dateTimeFormatSelected;
		$this->_HTMLTemplate->dateTimeFormatDisable  = $this->_dateTimeFormatDisable;
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
}