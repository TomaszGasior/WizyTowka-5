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
	protected $_userRequiredPermissions;
	protected $_userMustBeLoggedIn = true;
	protected $_alternativeLayout = false;

	protected $_currentUser;

	private $_HTMLLayout;
	private $_HTMLTopMenu;
	private $_HTMLMainMenu;
	protected $_HTMLHead;
	protected $_HTMLTemplate;
	protected $_HTMLContextMenu;
	protected $_HTMLMessage;

	final public function __construct()
	{
		if (SessionManager::isUserLoggedIn()) {
			$this->_currentUser = User::getById(SessionManager::getUserId());

			// Error handler details — only if user is logged in.
			if (Settings::get('adminPanelForceShowErrors')) {
				ErrorHandler::showErrorDetails(true);
			}
		}
		elseif ($this->_userMustBeLoggedIn) {
			$this->_redirect('login');
		}

		// When user have not required permissions to view this page of admin panel,
		// redirect him to permissions error message.
		if ($this->_userRequiredPermissions and !($this->_userRequiredPermissions & $this->_currentUser->permissions)) {
			$this->_redirect('error', ['type' => 'permissions']);
		}

		// Prepare HTML parts for child class.
		$this->_setupHTMLParts();

		// Run _prepare() method from child class.
		$this->_prepare();
	}

	final public function output()
	{
		// Top navigation menu and main navigation menu. Only for logged in users.
		if ($this->_userMustBeLoggedIn) {
			$this->_setupMenus();
		}

		// Run _output() method. Child class can specify additional template variables and context menu here.
		$this->_output();

		// Main HTML layout.
		$this->_HTMLLayout = new HTMLTemplate(
			$this->_alternativeLayout ? 'AdminPanelAlternate' : 'AdminPanelLayout',
			SYSTEM_DIR . '/templates'
		);
		$this->_HTMLLayout->head         = $this->_HTMLHead;
		$this->_HTMLLayout->topMenu      = $this->_HTMLTopMenu;
		$this->_HTMLLayout->mainMenu     = $this->_HTMLMainMenu;
		$this->_HTMLLayout->contextMenu  = $this->_HTMLContextMenu;
		$this->_HTMLLayout->message      = $this->_HTMLMessage;
		$this->_HTMLLayout->pageTitle    = $this->_pageTitle;
		$this->_HTMLLayout->pageTemplate = $this->_HTMLTemplate;

		// Recursively render all HTML elements and whole layout.
		$this->_HTMLLayout->render();
	}

	// This method sets up HTML layout parts needed by child class.
	private function _setupHTMLParts()
	{
		// HTML <head>.
		$this->_HTMLHead = new HTMLHead;
		$this->_HTMLHead->setAssetsPath(SYSTEM_URL . '/assets');
		$this->_HTMLHead->title($this->_pageTitle . ' — WizyTówka');
		$this->_HTMLHead->meta('viewport', 'width=device-width');
		$this->_HTMLHead->stylesheet('AdminMain.css');
		$this->_HTMLHead->stylesheet('AdminMobile.css');

		// Main template of page.
		$className = substr(strrchr(static::class, '\\'), 1);  // "WizyTowka\AdminPages\Pages" --> "Pages".
		$this->_HTMLTemplate = new HTMLTemplate($className, SYSTEM_DIR.'/templates/adminPages');

		// Context menu.
		$this->_HTMLContextMenu = new HTMLMenu;

		// HTML message box.
		$this->_HTMLMessage = new HTMLMessage;
	}

	// This method sets up admin panel menu elements according to current user permissions.
	private function _setupMenus()
	{
		// Top navigation menu.
		$this->_HTMLTopMenu = new HTMLMenu;
		$this->_HTMLTopMenu->add($this->_currentUser->name, self::URL('userSettings'),       'iconUser');
		$this->_HTMLTopMenu->add('Zaktualizuj',             self::URL('systemUpdate'),       'iconUpdates');
		$this->_HTMLTopMenu->add('Zobacz witrynę',          Settings::get('websiteAddress'), 'iconWebsite', null, ['target' => '_blank']);
		$this->_HTMLTopMenu->add('Wyloguj się',             self::URL('logout'),             'iconLogout');

		// Main navigation menu.
		$this->_HTMLMainMenu = new HTMLMenu;
		$add = function($label, $url, $CSSClass, $permission = null)
		{
			$this->_HTMLMainMenu->add(
				$label, $url, $CSSClass, null, [],
				$permission ? ($this->_currentUser->permissions & $permission) : true
			);
		};
		$add('Strony',             self::URL('pages'),             'iconPages');
		$add('Utwórz stronę',      self::URL('pageCreate'),        'iconAdd',           User::PERM_CREATING_PAGES);
		$add('Szkice',             self::URL('drafts'),            'iconDrafts');
		$add('Utwórz szkic',       self::URL('pageCreate', ['draft'=>true]), 'iconAdd', User::PERM_CREATING_PAGES);
		$add('Pliki',              self::URL('files'),             'iconFiles');
		$add('Wyślij pliki',       self::URL('filesSend'),         'iconAdd',           User::PERM_SENDING_FILES);
		$add('Menu',               self::URL('menus'),             'iconMenus' ,        User::PERM_EDITING_SITE_ELEMENTS);
		$add('Obszary',            self::URL('areas'),             'iconAreas' ,        User::PERM_EDITING_SITE_ELEMENTS);
		$add('Personalizacja',     self::URL('customization'),     'iconCustomization', User::PERM_EDITING_SITE_ELEMENTS);
		$add('Ustawienia',         self::URL('websiteSettings'),   'iconSettings',      User::PERM_EDITING_SITE_CONFIG);
		$add('Użytkownicy',        self::URL('users'),             'iconUsers',         User::PERM_SUPER_USER);
		$add('Utwórz użytkownika', self::URL('userCreate'),        'iconAdd',           User::PERM_SUPER_USER);
		$add('Edytor plików',      self::URL('dataEditor_List'),   'iconDataEditor',    User::PERM_SUPER_USER);
		$add('Utwórz plik',        self::URL('dataEditor_Editor'), 'iconAdd',           User::PERM_SUPER_USER);
		$add('Kopia zapasowa',     self::URL('backup'),            'iconBackup',        User::PERM_SUPER_USER);
		$add('Informacje',         self::URL('about'),             'iconInformation');
	}

	// Equivalent of Controller::__construct() method for AdminPanel child classes.
	protected function _prepare() {}

	// Equivalent of Controller::output() method for AdminPanel child classes.
	protected function _output() {}

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
			throw AdminPanelException::pageNameAlreadyRegistered($name);
		}
		if (!is_subclass_of($controller, self::class)) {
			throw AdminPanelException::pageControllerInvalid($name, self::class);
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