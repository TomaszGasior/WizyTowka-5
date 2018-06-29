<?php

/**
* WizyTówka 5
* Admin panel controller. It's a proxy for real admin pages controllers.
*/
namespace WizyTowka;

class AdminPanel extends Controller
{
	static private $_systemPagesNamespace = __NAMESPACE__ . '\AdminPages';
	static private $_pageAbstractClass    = __NAMESPACE__ . '\AdminPanelPage';

	static private $_registeredPages = [];

	private $_realAdminPanelPage;

	public function __construct()
	{
		// Force error handler details — only if user is logged in.
		if (SessionManager::isUserLoggedIn() and Settings::get('adminPanelForceShowErrors')) {
			ErrorHandler::showErrorDetails(true);
		}

		self::_prepareAutoloader();

		$pageName   = !empty($_GET['c']) ? $_GET['c'] : Settings::get('adminPanelDefaultPage');
		$controller = isset(self::$_registeredPages[$pageName]) ? self::$_registeredPages[$pageName]
		              : self::$_systemPagesNamespace . '\\'. ucfirst($pageName);

		if (!class_exists($controller)) {
			$this->_redirect(null);
		}

		// Create instance of real admin panel page controller.
		$this->_realAdminPanelPage = new $controller;
	}

	public function POSTQuery(...$arguments)
	{
		return $this->__call(__FUNCTION__, $arguments);
	}

	public function output(...$arguments)
	{
		return $this->__call(__FUNCTION__, $arguments);
	}

	public function __call($function, $arguments)
	{
		return $this->_realAdminPanelPage->$function(...$arguments);
	}

	static public function URL($target, array $arguments = [])
	{
		if ($target == null) {
			$target = Settings::get('adminPanelDefaultPage');
		}

		if (isset($arguments['c'])) {
			throw ControllerException::unallowedKeyInURLArgument('c');
		}
		$arguments = ['c' => $target] + $arguments;  // Adds "c" argument to array beginning for better URL readability.

		return Settings::get('adminPanelFile') . ($arguments ? '?' . http_build_query($arguments) : '');
	}

	static public function registerPage($name, $controller)
	{
		self::_prepareAutoloader();

		if (isset(self::$_registeredPages[$name])) {
			throw AdminPanelException::pageNameAlreadyRegistered($name);
		}
		if (!is_subclass_of($controller, self::$_pageAbstractClass)) {
			throw AdminPanelException::pageControllerInvalid($name, self::$_pageAbstractClass);
		}

		self::$_registeredPages[$name] = $controller;
	}

	static private function _prepareAutoloader()
	{
		// Add admin pages namespace to autoloader.
		if (!Autoloader::namespaceExists(self::$_systemPagesNamespace)) {
			Autoloader::addNamespace(self::$_systemPagesNamespace, SYSTEM_DIR . '/classes/AdminPages');
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