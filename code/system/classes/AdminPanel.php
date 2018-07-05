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
		// Add admin pages namespace to autoloader.
		WT()->autoloader->addNamespace(self::$_systemPagesNamespace, SYSTEM_DIR . '/classes/AdminPages');

		// Force error handler details — only if user is logged in.
		if (WT()->session->isUserLoggedIn() and WT()->settings->adminPanelForceShowErrors) {
			WT()->errors->setShowDetails(true);
		}

		$pageName   = $_GET['c'] ?? WT()->settings->adminPanelDefaultPage;
		$controller = self::$_registeredPages[$pageName] ?? self::$_systemPagesNamespace . '\\'. ucfirst($pageName);

		if (!class_exists($controller)) {
			$this->_redirect(null);
		}

		// Create instance of real admin panel page controller.
		$this->_realAdminPanelPage = new $controller;
	}

	public function POSTQuery(...$arguments) : void
	{
		$this->__call(__FUNCTION__, $arguments);
	}

	public function output(...$arguments) : void
	{
		$this->__call(__FUNCTION__, $arguments);
	}

	public function __call(string $function, array $arguments)
	{
		return $this->_realAdminPanelPage->$function(...$arguments);
	}

	static public function URL($target, array $arguments = []) : string
	{
		if ($target == null) {
			$target = WT()->settings->adminPanelDefaultPage;
		}

		$target = (string)$target;

		if (isset($arguments['c'])) {
			throw ControllerException::unallowedKeyInURLArgument('c');
		}
		$arguments = ['c' => $target] + $arguments;  // Adds "c" argument to array beginning for better URL readability.

		return WT()->settings->adminPanelFile . ($arguments ? '?' . http_build_query($arguments) : '');
	}

	static public function registerPage(string $name, string $controller) : void
	{
		// Add admin pages namespace to autoloader, earlier.
		WT()->autoloader->addNamespace(self::$_systemPagesNamespace, SYSTEM_DIR . '/classes/AdminPages');

		if (isset(self::$_registeredPages[$name])) {
			throw AdminPanelException::pageNameAlreadyRegistered($name);
		}
		if (!is_subclass_of($controller, self::$_pageAbstractClass)) {
			throw AdminPanelException::pageControllerInvalid($name, self::$_pageAbstractClass);
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