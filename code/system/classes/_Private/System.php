<?php

/**
* WizyTówka 5
* System class — initializes system and manages services. Used in WT() function.
*/
namespace WizyTowka\_Private;
use WizyTowka as __;

class System
{
	private const TOPLEVEL_NAMESPACE = 'WizyTowka';

	private $_isInitialized;

	// These properties are public and read only, without "__" prefix.
	private $__autoloader;
	private $__database;
	private $__errors;
	private $__hooks;
	private $__session;
	private $__settings;

	// Prepares autoloader and error handler. It's used by WT() function inside "init.php".
	public function __construct()
	{
		require_once __DIR__ . '/Autoloader.php';

		mb_internal_encoding('UTF-8');
		mb_regex_encoding('UTF-8');

		// Autoloader.
		$this->__autoloader = new Autoloader;

		$this->__autoloader->addNamespace(self::TOPLEVEL_NAMESPACE, __\SYSTEM_DIR . '/classes');
		$this->__autoloader->addNamespace(__NAMESPACE__,            __\SYSTEM_DIR . '/classes/_Private');

		spl_autoload_register([$this->__autoloader, 'autoload']);

		// Error handler.
		$this->__errors = new ErrorHandler;

		set_error_handler([$this->__errors, 'handleError']);
		set_exception_handler([$this->__errors, 'handleException']);
	}

	// This method is used to simulate read only properties.
	public function __get(string $name) /*: ?object*/   // Backward compatibility with PHP 7.1.
	{
		if ($this->{'__' . $name}) {
			return $this->{'__' . $name};
		}

		// Following objects are available only in installed system.
		if (!$this->_isInitialized) {
			return null;
		}

		// Delayed classes initialization.
		switch ($name) {
			// Main configuration file.
			case 'settings':
				$value = new __\ConfigurationFile(__\CONFIG_DIR . '/settings.conf');
				break;

			// Hooks manager.
			case 'hooks':
				$value = new Hooks;
				break;

			// Session manager.
			case 'session':
				$value = new SessionManager('WTCMSSession', new __\ConfigurationFile(__\CONFIG_DIR . '/sessions.conf'));
				break;

			// PDO connection.
			case 'database':
				$value = new DatabasePDO(
					$this->__settings->databaseType,
					($this->__settings->databaseType == 'sqlite') ? __\CONFIG_DIR . '/database.db' : $this->__settings->databaseName,
					$this->__settings->databaseHost, $this->__settings->databaseUsername, $this->__settings->databasePassword
				);
				break;
		}

		return $this->{'__' . $name} = $value;
	}

	// This method starts system controller and prepares settings needed only in installed system.
	// It's used by WT() function with argument, inside "admin.php" and "index.php".
	// Should not be called inside unit tests or utility scripts.
	public function __invoke(string $controllerName) : void
	{
		if ($this->_isInitialized) { return; } $this->_isInitialized = true;

		// Installer.
		$isInstalled = is_file(__\CONFIG_DIR . '/settings.conf');
		if (!$isInstalled) {
			$this->_runController(new Installer);
			exit;
		}

		// Error handler — log file.
		$this->__errors->setLogFilePath(__\CONFIG_DIR . '/errors.log');

		// Apply PHP settings.
		$settings = $this->settings;

		if ($settings->phpSettingsLocales) {
			setlocale(LC_ALL, explode('|', $settings->phpSettingsLocales));
		}
		if ($settings->phpSettingsTimeZone) {
			date_default_timezone_set($settings->phpSettingsTimeZone);
		}

		// Error handler — errors details.
		if (!$settings->systemShowErrors) {
			$this->__errors->setShowDetails(false);
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
		if (property_exists($this, '__' . $name)) {
			$this->{'__' . $name} = $value;
		}
	}

	public function getDefaultSettings() : __\ConfigurationFile
	{
		return new __\ConfigurationFile(__\SYSTEM_DIR . '/defaults/settings.conf', true); // Read only.
	}
}