<?php

/**
* WizyTówka 5
* Admin panel abstract controller.
*/
namespace WizyTowka;

abstract class AdminPanel extends Controller
{
	protected $_pageTitle = 'Panel administracyjny';
	protected $_requiredUserPermissions;

	private $_apLayout;
	private $_apTopMenu;
	private $_apMainMenu;

	protected $_apTemplate;
	protected $_apHead;
	protected $_apContextMenu;
	protected $_apMessage;
	protected $_apMessageError = false;

	public function __construct()
	{
		$this->_prepare();
	}

	public function output()
	{
		// HTML <head>.
		$this->_head = new HTMLHead;
		$this->_head->setTitle($this->_pageTitle . ' — WizyTówka');
		$this->_head->addStyle('AdminMain.css');

		// Top navigation menu.
		$this->_apTopMenu = new HTMLMenu;
		$this->_apTopMenu->add('tomaszgasior', self::URL('userSettings'), 'iUser');
		$this->_apTopMenu->add('Zaktualizuj', self::URL('systemUpdate'), 'iUpdates');
		$this->_apTopMenu->add('Zobacz witrynę', Settings::get('websiteAddress'), 'iWebsite');
		$this->_apTopMenu->add('Wyloguj się', self::URL('logout'), 'iLogout');

		// Main navigation menu.
		$this->_apMainMenu = new HTMLMenu;
		$this->_apMainMenu->add('Strony', self::URL('pages'), 'iPages');
		$this->_apMainMenu->add('Utwórz stronę', self::URL('pageCreate'), 'iAdd');
		$this->_apMainMenu->add('Szkice', self::URL('drafts'), 'iDrafts');
		$this->_apMainMenu->add('Utwórz szkic', self::URL('pageCreate', ['draft' => true]), 'iAdd');
		$this->_apMainMenu->add('Pliki', self::URL('files'), 'iFiles');
		$this->_apMainMenu->add('Wyślij pliki', self::URL('filesSend'), 'iAdd');
		$this->_apMainMenu->add('Użytkownicy', self::URL('users'), 'iUsers');
		$this->_apMainMenu->add('Utwórz użytkownika', self::URL('userCreate'), 'iAdd');
		$this->_apMainMenu->add('Menu', self::URL('menus'), 'iMenus');
		$this->_apMainMenu->add('Obszary', self::URL('widgets'), 'iWidgets');
		$this->_apMainMenu->add('Ustawienia', self::URL('settings'), 'iSettings');
		$this->_apMainMenu->add('Personalizacja', self::URL('customization'), 'iCustomization');
		$this->_apMainMenu->add('Edytor plików', self::URL('filesEditor'), 'iFilesEditor');
		$this->_apMainMenu->add('Kopia zapasowa', self::URL('backup'), 'iBackup');
		$this->_apMainMenu->add('Informacje', self::URL('informations'), 'iInformations');

		// Context menu.
		$this->_apContextMenu = new HTMLMenu;

		// Prepare template to use in _output() in child method.
		$className = substr(strrchr(static::class, '\\'), 1);
		$this->_apTemplate = new HTMLTemplate($className);

		$this->_output();

		// Main HTML layout.
		$this->_apLayout = new HTMLTemplate;
		$this->_apLayout->head         = $this->_head;
		$this->_apLayout->topMenu      = $this->_apTopMenu;
		$this->_apLayout->mainMenu     = $this->_apMainMenu;
		$this->_apLayout->contextMenu  = $this->_apContextMenu;
		$this->_apLayout->message      = $this->_apMessage;
		$this->_apLayout->messageError = $this->_apMessageError;
		$this->_apLayout->pageTitle    = $this->_pageTitle;
		$this->_apLayout->pageTemplate = $this->_apTemplate;
		$this->_apLayout->render('AP_Layout');
	}

	abstract protected function _prepare();
	// Equivalent of __construct() method for child classes.

	abstract protected function _output();
	// Equivalent of output() method for child classes.

	static public function URL($target, $arguments = [])
	{
		if (isset($arguments['c'])) {
			throw new Exception('Argument of admin panel URL must not have key named "c".', 25);
		}
		$arguments = ['c' => $target] + $arguments;  // Adds "c" argument to begin of array for better readability.

		return Settings::get('systemAdminPanelFile') . ($arguments ? '?'.http_build_query($arguments) : '');
	}
}