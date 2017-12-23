<?php

/**
* WizyTówka 5
* Admin panel controller. It's a proxy for real admin pages controllers.
*/
namespace WizyTowka;

class AdminPanel extends Controller
{
	static private $_defaultPagesNamespace = __NAMESPACE__ . '\AdminPages';
	static private $_defaultPagesAbstract  = __NAMESPACE__ . '\AdminPanelPage';

	static private $_registeredPages = [];

	private $_realAdminPanelPage;

	public function __construct()
	{
		// Force error handler details — only if user is logged in.
		if (SessionManager::isUserLoggedIn() and Settings::get('adminPanelForceShowErrors')) {
			ErrorHandler::showErrorDetails(true);
		}

		// Add admin pages namespace to autoloader.
		Autoloader::addNamespace(self::$_defaultPagesNamespace, SYSTEM_DIR . '/classes/AdminPages');

		$pageName = !empty($_GET['c']) ? $_GET['c'] : Settings::get('adminPanelDefaultPage');

		$controller = isset(self::$_registeredPages[$pageName]) ? self::$_registeredPages[$pageName]
		              : self::$_defaultPagesNamespace . '\\'. ucfirst($pageName);

		// Redirect to default admin page if class does not exists.
		if (!class_exists($controller)) {
			$this->_redirect(
				// Use default setting if user changed it improperly in configuration file to keep admin panel working.
				empty($_GET['c']) ? Settings::getDefault('adminPanelDefaultPage') : null
			);
		}

		// Create instance of real admin panel page controller.
		$this->_realAdminPanelPage = new $controller;
	}

	public function filterPOSTData()
	{
		$this->_realAdminPanelPage->{__FUNCTION__}();
	}

	public function POSTQuery()
	{
		$this->_realAdminPanelPage->{__FUNCTION__}();
	}

	public function output()
	{
		$this->_realAdminPanelPage->{__FUNCTION__}();
	}

	public function __call($function, $arguments)
	{
		$this->_realAdminPanelPage->$function(...$arguments);
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

		return Settings::get('adminPanelFile') . ($arguments ? '?'.http_build_query($arguments) : '');
	}

	static public function registerPage($name, $controller)
	{
		if (isset(self::$_registeredPages[$name])) {
			throw AdminPanelException::pageNameAlreadyRegistered($name);
		}
		if (!is_subclass_of($controller, self::$_defaultPagesAbstract)) {
			throw AdminPanelException::pageControllerInvalid($name, self::$_defaultPagesAbstract);
		}

		self::$_registeredPages[$name] = $controller;
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