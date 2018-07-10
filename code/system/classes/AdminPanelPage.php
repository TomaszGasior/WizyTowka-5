<?php

/**
* WizyTówka 5
* Admin panel page abstract controller.
*/
namespace WizyTowka;

abstract class AdminPanelPage extends Controller
{
	protected $_pageTitle = 'Panel administracyjny';
	protected $_userRequiredPermissions;
	protected $_userMustBeLoggedIn = true;

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
		if (WT()->session->isUserLoggedIn()) {
			$this->_currentUser = User::getById(WT()->session->getUserId());

			// Prevent access to this page of admin panel if user have not required permissions.
			if ($this->_userRequiredPermissions and !($this->_currentUser->permissions & $this->_userRequiredPermissions)) {
				// Fallback used if current user have not permission to see default page.
				if ($_GET['c'] ?? '' == WT()->settings->adminPanelDefaultPage) {
					$this->_redirect('about');
				}

				$this->_redirect('error', ['type' => 'permissions']);
			}
		}
		elseif ($this->_userMustBeLoggedIn) {
			$this->_redirect(
				'login',
				!empty($_SERVER['QUERY_STRING']) ? ['redirect' => $_SERVER['QUERY_STRING']] : []
			);
		}

		// Prepare HTML parts here for child class.
		$this->_setupHTMLParts();

		// Run _prepare() method from child class.
		$this->_prepare();
	}

	// This method sets up HTML layout parts needed by child class.
	private function _setupHTMLParts() : void
	{
		// HTML <head>.
		$this->_HTMLHead = new HTMLHead;
		$this->_HTMLHead->setAssetsPath(SYSTEM_URL . '/assets');
		$this->_HTMLHead->setTitlePattern('%s — WizyTówka');
		$this->_HTMLHead->title($this->_pageTitle);
		$this->_HTMLHead->meta('viewport', 'width=device-width');
		$this->_HTMLHead->meta('theme-color', '#232323');
		$this->_HTMLHead->stylesheet('AdminMain.css');
		$this->_HTMLHead->stylesheet('AdminIcons.css');
		$this->_HTMLHead->stylesheet('https://fonts.googleapis.com/css?family=Lato:400,700&subset=latin-ext');

		// Main template of page.
		$className = substr(strrchr(static::class, '\\'), 1);  // "WizyTowka\AdminPages\Pages" --> "Pages".
		$this->_HTMLTemplate = new HTMLTemplate($className, SYSTEM_DIR.'/templates/AdminPages');

		// Context menu.
		$this->_HTMLContextMenu = new HTMLMenu;

		// HTML message box.
		$this->_HTMLMessage = new HTMLMessage(null, 'AdminPanel');

		// Top navigation menu and main navigation menu. Only for logged in users.
		if ($this->_userMustBeLoggedIn) {
			$this->_setupMenus();
		}
	}

	// This method sets up admin panel main menu elements according to current user permissions.
	private function _setupMenus() : void
	{
		// Top navigation menu.
		$this->_HTMLTopMenu = new HTMLMenu;
		$this->_HTMLTopMenu->append($this->_currentUser->name, self::URL('preferences'), 'iconUser');
		if (false) {
			$this->_HTMLTopMenu->append('Zaktualizuj', self::URL('systemUpdate'), 'iconUpdates');
		}
		$this->_HTMLTopMenu->append('Zobacz witrynę', WT()->settings->websiteAddress, 'iconWebsite', ['target' => '_blank']);
		$this->_HTMLTopMenu->append('Wyloguj się',    self::URL('logout'),            'iconLogout');

		// Main navigation menu.
		$this->_HTMLMainMenu = new HTMLMenu;
		$add = function($label, $url, $CSSClass, $permission = null, $lockdown = null)
		{
			$hasPermission = $permission ? ($this->_currentUser->permissions & $permission) : true;
			$isLockdowned  = $lockdown   ? WT()->settings->{'lockdown' . $lockdown}         : false;
			$this->_HTMLMainMenu->append($label, $url, $CSSClass, [], $hasPermission and !$isLockdowned);
		};
		$add('Strony',             self::URL('pages'),             'iconPages',           User::PERM_MANAGE_PAGES);
		$add(
			'Utwórz stronę',       self::URL('pageCreate'),        'iconAdd',
			($this->_currentUser->permissions & User::PERM_CREATE_PAGES) ? User::PERM_PUBLISH_PAGES : User::PERM_CREATE_PAGES
		);
		$add('Szkice',             self::URL('pages',     ['drafts' => 1]), 'iconDrafts', User::PERM_MANAGE_PAGES);
		$add('Utwórz szkic',       self::URL('pageCreate', ['draft' => 1]), 'iconAdd',    User::PERM_CREATE_PAGES);
		$add('Pliki',              self::URL('files'),             'iconFiles',           User::PERM_MANAGE_FILES);
		$add('Wyślij pliki',       self::URL('filesSend'),         'iconAdd',             User::PERM_MANAGE_FILES);
		$add('Menu',               self::URL('menus'),             'iconMenus' ,          User::PERM_WEBSITE_ELEMENTS);
		$add('Obszary',            self::URL('areas'),             'iconAreas' ,          User::PERM_WEBSITE_ELEMENTS);
		$add('Personalizacja',     self::URL('customization'),     'iconCustomization',   User::PERM_WEBSITE_SETTINGS);
		$add('Ustawienia',         self::URL('websiteSettings'),   'iconSettings',        User::PERM_WEBSITE_SETTINGS);
		$add('Użytkownicy',        self::URL('users'),             'iconUsers',           User::PERM_SUPER_USER,  'Users');
		$add('Utwórz użytkownika', self::URL('userCreate'),        'iconAdd',             User::PERM_SUPER_USER,  'Users');
		$add('Edytor plików',      self::URL('dataEditor_List'),   'iconDataEditor',      User::PERM_SUPER_USER,  'DataEditor');
		$add('Utwórz plik',        self::URL('dataEditor_Editor'), 'iconAdd',             User::PERM_SUPER_USER,  'DataEditor');
		$add('Kopia zapasowa',     self::URL('backup'),            'iconBackup',          User::PERM_SUPER_USER,  'Backup');
		$add('Informacje',         self::URL('about'),             'iconAbout');
	}

	final public function output() : void
	{
		// Run _output() method. Child class can specify additional template variables and context menu here.
		$this->_output();

		// Main HTML layout.
		$this->_HTMLLayout = new HTMLTemplate(
			$this->_userMustBeLoggedIn ? 'AdminPanelLayout' : 'AdminPanelAlternative',
			SYSTEM_DIR . '/templates'
		);
		$this->_HTMLLayout->head         = $this->_HTMLHead;
		$this->_HTMLLayout->topMenu      = $this->_HTMLTopMenu;
		$this->_HTMLLayout->mainMenu     = $this->_HTMLMainMenu;
		$this->_HTMLLayout->contextMenu  = $this->_HTMLContextMenu;
		$this->_HTMLLayout->message      = $this->_HTMLMessage;
		$this->_HTMLLayout->pageTitle    = $this->_pageTitle;
		$this->_HTMLLayout->pageTemplate = $this->_HTMLTemplate;
		$this->_HTMLLayout->id           = lcfirst($_GET['c'] ?? '');

		// Recursively render all HTML elements and whole layout.
		$this->_HTMLLayout->render();
	}

	// Equivalent of Controller::__construct() method for AdminPanel child classes.
	protected function _prepare() : void {}

	// Equivalent of Controller::output() method for AdminPanel child classes.
	protected function _output() : void {}

	static public function URL($target, array $arguments = []) : string
	{
		return AdminPanel::URL(...func_get_args());
	}
}