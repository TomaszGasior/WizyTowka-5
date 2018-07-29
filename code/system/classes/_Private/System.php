<?php

/**
* WizyTówka 5
* System class — initializes system and manages services. Used in WT() function.
*/
namespace WizyTowka\_Private;
use WizyTowka as __;

/**
* @property Autoloader           $autoloader
* @property DatabasePDO          $database
* @property ErrorHandler         $errors
* @property HooksManager         $hooks
* @property SessionManager       $session
* @property __\ConfigurationFile $settings
*/
class System
{
	private const TOPLEVEL_NAMESPACE = 'WizyTowka';

	private $_isInitialized;

	// These array items will be accessible as public and read only properties.
	private $_srv = [];

	// Prepares autoloader and error handler. It's used by WT() function inside "init.php".
	public function __construct()
	{
		require_once __DIR__ . '/Autoloader.php';

		mb_internal_encoding('UTF-8');
		mb_regex_encoding('UTF-8');

		// Autoloader.
		$this->_srv['autoloader'] = new Autoloader;

		$this->_srv['autoloader']->addNamespace(self::TOPLEVEL_NAMESPACE, __\SYSTEM_DIR . '/classes');
		$this->_srv['autoloader']->addNamespace(__NAMESPACE__,            __\SYSTEM_DIR . '/classes/_Private');

		spl_autoload_register([$this->_srv['autoloader'], 'autoload']);

		// Error handler.
		$this->_srv['errors'] = new ErrorHandler;

		set_error_handler([$this->_srv['errors'], 'handleError']);
		set_exception_handler([$this->_srv['errors'], 'handleException']);

		// Make globals read only.
		$_GET    = new ReadOnlyArray($_GET,    '_GET');
		$_POST   = new ReadOnlyArray($_POST,   '_POST');
		$_FILES  = new ReadOnlyArray($_FILES,  '_FILES');
		$_SERVER = new ReadOnlyArray($_SERVER, '_SERVER');
		$_COOKIE = new ReadOnlyArray($_COOKIE, '_COOKIE');
	}

	// This method is used to simulate read only properties. Redundant syntax is used for better performance.
	public function __get(string $name) /*: ?object*/   // Backward compatibility with PHP 7.1.
	{
		// These properties are available only inside installed and initialized system
		// or when are overwritten manually inside unit tests or utility scripts.
		if (!$this->_isInitialized and !isset($this->_srv[$name])) {
			return null;
		}

		switch ($name) {
			// Main configuration file.
			case 'settings':
				return $this->_srv[$name] ?? $this->_srv[$name] =
					new __\ConfigurationFile(__\CONFIG_DIR . '/settings.conf');

			// Hooks manager.
			case 'hooks':
				return $this->_srv[$name] ?? $this->_srv[$name] =
					new HooksManager;

			// Session manager.
			case 'session':
				return $this->_srv[$name] ?? $this->_srv[$name] =
					new SessionManager(
						'WTCMS__' . $this->settings->sessionCookiePart,
						new __\ConfigurationFile(__\CONFIG_DIR . '/sessions.conf')
					);

			// PDO connection.
			case 'database':
				return $this->_srv[$name] ?? $this->_srv[$name] =
					new DatabasePDO(
						($settings = $this->settings)->databaseType,
						($settings->databaseType == 'sqlite' ? __\CONFIG_DIR . '/database.db' : $settings->databaseName),
						$settings->databaseHost, $settings->databaseUsername, $settings->databasePassword
					);

			case 'autoloader':
			case 'errors':
				return $this->_srv[$name];

			default:
				trigger_error('Undefined property: ' . __CLASS__ . '::$' . $name, E_USER_NOTICE);  // Follow native PHP behavior.
				return null;
		}
	}

	// Don't allow to overwrite read only properties simply by setting new value.
	public function __set(string $name, $value) : void {}

	// This method starts system controller and prepares settings needed only in installed system.
	// It's used by WT() function with argument, inside "admin.php" and "index.php".
	// Should not be called inside unit tests or utility scripts.
	public function __invoke(string $controllerName) : void
	{
		if ($this->_isInitialized) { return; } $this->_isInitialized = true;

		// Installer.
		$isInstalled = is_file(__\CONFIG_DIR . '/settings.conf');

		if (!$isInstalled) {
			$this->_runController(new InstallationWizard);
			exit;
		}

		// Error handler's log file.
		$this->_srv['errors']->setLogFilePath(__\CONFIG_DIR . '/errors.log');

		// Apply settings.
		$settings = $this->settings;

		$this->_srv['errors']->setShowDetails($settings->systemShowErrors);

		if ($settings->phpSettingsLocales) {
			setlocale(LC_ALL, explode('|', $settings->phpSettingsLocales));
		}
		if ($settings->phpSettingsTimeZone) {
			date_default_timezone_set($settings->phpSettingsTimeZone);
		}

		// Init plugins.
		foreach (__\Plugin::getAll() as $plugin) {
			$plugin->init();
		}

		// Set up system hooks.
		$this->hooks->runAction('Init');
		register_shutdown_function(function(){ $this->hooks->runAction('Shutdown'); });

		// Init controller.
		$controllerClass = self::TOPLEVEL_NAMESPACE . '\\' . $controllerName;
		$this->_runController(new $controllerClass);

		// Run closing hook.
		$this->hooks->runAction('End');
	}

	private function _runController(__\Controller $controller) : void
	{
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$controller->POSTQuery();
		}

		$controller->output();
	}

	// Replaces read only property. Intented for unit tests and utility scripts. Don't use it.
	public function overwrite(string $name, /*?object*/ $value) : void  // Backward compatibility with PHP 7.1.
	{
		$this->_srv[$name] = $value;
	}

	public function getDefaultSettings() : __\ConfigurationFile
	{
		return new __\ConfigurationFile(__\SYSTEM_DIR . '/defaults/settings.conf', true); // Read only.
	}
}