<?php

/**
* WizyTówka 5
* Admin panel abstract controller.
*/
namespace WizyTowka;

abstract class AdminPanel extends Controller
{
	static private $_registeredPages = [];
	static private $_defaultPagesNamespace = __NAMESPACE__ . '\AdminPages';

	protected $_pageTitle = 'Panel administracyjny';

	protected $_currentUser;
	protected $_userRequiredPermissions;
	protected $_userMustBeLoggedIn = true;

	private $_apLayout;
	private $_apTopMenu;
	private $_apMainMenu;

	protected $_apAlternateLayout = false;
	protected $_apHead;
	protected $_apTemplate;
	protected $_apContextMenu;
	protected $_apMessage;
	protected $_apMessageError = false;

	final public function __construct()
	{
		if (SessionManager::isUserLoggedIn()) {
			$this->_currentUser = User::getById(SessionManager::getUserId());
		}
		elseif ($this->_userMustBeLoggedIn) {
			$this->_redirect(self::URL('login'));
		}

		// When user have not required permissions to view this page of admin panel,
		// redirect him to permissions error message.
		if ($this->_userRequiredPermissions and !($this->_userRequiredPermissions & $this->_currentUser->permissions)) {
			$this->_redirect(self::URL('permissionsError'));
		}

		// Run _prepare() method from child class.
		$this->_prepare();
	}

	final public function output()
	{
		// HTML <head>.
		$this->_apHead = new HTMLHead;
		$this->_apHead->setTitle($this->_pageTitle . ' — WizyTówka');
		$this->_apHead->setAssetsPath(basename(SYSTEM_DIR).'/assets');
		$this->_apHead->setMeta('viewport', 'width=device-width');
		$this->_apHead->addStyle('AdminMain.css');
		$this->_apHead->addStyle('AdminMobile.css');

		// Top navigation menu and main navigation menu.
		if (!$this->_apAlternateLayout) {
			$this->_setupMenus();
		}

		// Context menu (prepared for child class).
		$this->_apContextMenu = new HTMLMenu;

		// Main template of page (prepared for child class).
		$className = substr(strrchr(static::class, '\\'), 1);  // Example: "WizyTowka\AdminPages\Pages" --> "Pages".
		$this->_apTemplate = new HTMLTemplate($className, SYSTEM_DIR.'/templates/adminPages');

		// Run _output() method. Child class can specify additional template variables and context menu here.
		$this->_output();

		// Main HTML layout.
		$this->_apLayout = new HTMLTemplate(null, SYSTEM_DIR.'/templates');
		$this->_apLayout->setTemplate($this->_apAlternateLayout ? 'APAlternateLayout' : 'APStandardLayout');
		$this->_apLayout->head         = $this->_apHead;
		$this->_apLayout->topMenu      = $this->_apTopMenu;
		$this->_apLayout->mainMenu     = $this->_apMainMenu;
		$this->_apLayout->contextMenu  = $this->_apContextMenu;
		$this->_apLayout->message      = $this->_apMessage;
		$this->_apLayout->messageError = $this->_apMessageError;
		$this->_apLayout->pageTitle    = $this->_pageTitle;
		$this->_apLayout->pageTemplate = $this->_apTemplate;

		// Recursively render all HTML elements and whole layout.
		$this->_apLayout->render();
	}

	private function _setupMenus()
	{
		// Top navigation menu.
		$this->_apTopMenu = new HTMLMenu;
		$this->_apTopMenu->add($this->_currentUser->name, self::URL('userSettings'), 'iconUser');
		$this->_apTopMenu->add('Zaktualizuj', self::URL('systemUpdate'), 'iconUpdates');
		$this->_apTopMenu->add('Zobacz witrynę', Settings::get('websiteAddress'), 'iconWebsite', null, true);
		$this->_apTopMenu->add('Wyloguj się', self::URL('logout'), 'iconLogout');

		// Main navigation menu.
		$this->_apMainMenu = new HTMLMenu;
		$this->_apMainMenu->add('Strony', self::URL('pages'), 'iconPages');
		if ($this->_currentUser->permissions & User::PERM_CREATING_PAGES) {
			$this->_apMainMenu->add('Utwórz stronę', self::URL('pageCreate'), 'iconAdd');
		}
		$this->_apMainMenu->add('Szkice', self::URL('drafts'), 'iconDrafts');
		if ($this->_currentUser->permissions & User::PERM_CREATING_PAGES) {
			$this->_apMainMenu->add('Utwórz szkic', self::URL('pageCreate', ['draft' => true]), 'iconAdd');
		}
		$this->_apMainMenu->add('Pliki', self::URL('files'), 'iconFiles');
		if ($this->_currentUser->permissions & User::PERM_SENDING_FILES) {
			$this->_apMainMenu->add('Wyślij pliki', self::URL('filesSend'), 'iconAdd');
		}
		if ($this->_currentUser->permissions & User::PERM_EDITING_SITE_ELEMENTS) {
			$this->_apMainMenu->add('Menu', self::URL('menus'), 'iconMenus');
			$this->_apMainMenu->add('Obszary', self::URL('areas'), 'iconAreas');
			$this->_apMainMenu->add('Personalizacja', self::URL('customization'), 'iconCustomization');
		}
		if ($this->_currentUser->permissions & User::PERM_EDITING_SITE_CONFIG) {
			$this->_apMainMenu->add('Ustawienia', self::URL('siteSettings'), 'iconSettings');
		}
		if ($this->_currentUser->permissions & User::PERM_SUPER_USER) {
			$this->_apMainMenu->add('Użytkownicy', self::URL('users'), 'iconUsers');
			$this->_apMainMenu->add('Utwórz użytkownika', self::URL('userCreate'), 'iconAdd');
			$this->_apMainMenu->add('Edytor plików', self::URL('dataEditor_List'), 'iconDataEditor');
			$this->_apMainMenu->add('Utwórz plik', self::URL('dataEditor_Editor'), 'iconAdd');
			$this->_apMainMenu->add('Kopia zapasowa', self::URL('backup'), 'iconBackup');
		}
		$this->_apMainMenu->add('Informacje', self::URL('informations'), 'iconInformation');
	}

	protected function _prepare() {}
	// Equivalent of Controller::__construct() method for AdminPanel child classes.

	protected function _output() {}
	// Equivalent of Controller::output() method for AdminPanel child classes.

	static public function URL($target, array $arguments = [])
	{
		if ($target == null) {
			$target = Settings::get('adminPanelDefaultPage');
		}

		if (isset($arguments['c'])) {
			throw ControllerException::unallowedKeyInURLArgument('c');
		}
		$arguments = ['c' => $target] + $arguments;  // Adds "c" argument to array beginning for better URL readability.

		return Settings::get('adminPanelFile') . ($arguments ? '?'.http_build_query($arguments) : '');
	}

	static public function registerPage($name, $controller)
	{
		if (isset(self::$_registeredPages[$name]) or class_exists(self::$_defaultPagesNamespace.'\\'.ucfirst($name))) {
			AdminPanelException::pageNameAlreadyRegistered($name);
		}
		if (!is_subclass_of($controller, self::class)) {
			AdminPanelException::pageControllerInvalid($name, self::class);
		}

		self::$_registeredPages[$name] = $controller;
	}

	static public function getControllerClass()
	{
		$pageName = empty($_GET['c']) ? null : $_GET['c'];

		if (isset(self::$_registeredPages[$pageName])) {
			return self::$_registeredPages[$pageName];
		}
		else {
			$controller        = self::$_defaultPagesNamespace . '\\'. ucfirst($pageName);
			$defaultController = self::$_defaultPagesNamespace . '\\'. ucfirst(Settings::get('adminPanelDefaultPage'));

			Autoloader::addNamespace(self::$_defaultPagesNamespace, SYSTEM_DIR.'/classes/adminPages');
			return class_exists($controller) ? $controller : $defaultController;
		}
	}
}

class AdminPanelException extends Exception
{
	static public function pageNameAlreadyRegistered($name)
	{
		return new self('Page name "' . $name . '" is already registered in admin panel.', 1);
	}
	static public function pageControllerInvalid($name, $class)
	{
		return new self('Controller of admin panel page named "' . $name . '" is invalid. Controller must extend ' . $class . ' class.', 2);
	}
}