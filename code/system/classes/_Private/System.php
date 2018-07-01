<?php

/**
* WizyTówka 5
* System class — initializes system and manages services. Used in WT() function.
*/
namespace WizyTowka\_Private;
use WizyTowka as __;

class System
{
	const TOPLEVEL_NAMESPACE = 'WizyTowka';

	private $_isInstalled;
	private $_isInitialized;

	// These field are public and read only, without "__" prefix.
	private $__autoloader;
	private $__database;
	private $__errors;
	private $__hooks;
	private $__session;
	private $__settings;

	public function __construct()
	{
		$this->_isInstalled = is_file(__\CONFIG_DIR . '/settings.conf');

		require_once __DIR__ . '/Autoloader.php';

		mb_internal_encoding('UTF-8');
		mb_regex_encoding('UTF-8');

		// Autoloader.
		$this->__autoloader = new Autoloader;

		$this->__autoloader->addNamespace(self::TOPLEVEL_NAMESPACE, __\SYSTEM_DIR . '/classes');
		$this->__autoloader->addNamespace(__NAMESPACE__,            __\SYSTEM_DIR . '/classes/_Private');

		spl_autoload_register([$this->__autoloader, 'autoload']);

		// Error handler.
		$this->__errors = new ErrorHandler(__\CONFIG_DIR . '/errors.log');

		set_error_handler([$this->__errors, 'handleError']);
		set_exception_handler([$this->__errors, 'handleException']);

		// Settings.
		$this->_isInstalled
		? $this->__settings = new __\ConfigurationFile(__\CONFIG_DIR . '/settings.conf')
		: $this->__settings = $this->getDefaultSettings();

		// PHP settings.
		setlocale(LC_ALL, explode('|', $this->__settings->phpLocalesList));
		date_default_timezone_set($this->__settings->phpTimeZone);

		// Installer.
		if (!$this->_isInstalled) {
			$this->_runController(new Installer);
			return;
		}

		// Error handler — details setting.
		$this->__errors->showErrorDetails($this->__settings->systemShowErrors);

		// Hooks manager.
		$this->__hooks = new Hooks;
	}

	public function __get($name)
	{
		if ($this->{'__' . $name}) {
			return $this->{'__' . $name};
		}

		// Following objects are available only in installed system.
		if (!$this->_isInstalled) {
			return;
		}

		// Delayed classes initialization.
		switch ($name) {
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

	public function __invoke($controllerName)
	{
		if ($this->_isInitialized) {
			return;
		}
		$this->_isInitialized = true;

		// Init plugins.
		foreach (__\Plugin::getAll() as $plugin) {
			$plugin->init();
		}

		// Init controller.
		$controllerClass = self::TOPLEVEL_NAMESPACE . '\\' . $controllerName;
		$this->_runController(new $controllerClass);
	}

	private function _runController(__\Controller $controller)
	{
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$controller->POSTQuery();
		}

		$controller->output();
	}

	public function overwrite($name, $value)
	{
		if (property_exists($this, '__' . $name)) {
			$this->{'__' . $name} = $value;
		}
	}

	public function getDefaultSettings()
	{
		return new __\ConfigurationFile(__\SYSTEM_DIR . '/defaults/settings.conf', true); // Read only.
	}
}