<?php

/**
* WizyTówka 5 — unit test
*/
class AdminPanelTest extends PHPUnit\Framework\TestCase
{
	static private $_examplePageName = 'example';
	static private $_examplePageClass;

	static public function setUpBeforeClass()
	{
		$exampleAdminPanelClass = new class() extends WizyTowka\AdminPanel
		{
			protected $_userMustBeLoggedIn = false;
			// It's needed to run test. Without it AdminPanel's constructor runs Controller::redirect() method, which exits script.

			public function _prepare() {}
			public function _output() {}
		};

		self::$_examplePageClass = get_class($exampleAdminPanelClass);
	}

	public function testRegisterPage()
	{
		WizyTowka\AdminPanel::registerPage(self::$_examplePageName, self::$_examplePageClass);

		$_GET['c'] = self::$_examplePageName;
		// AdminPanel::getControllerClass() reads name of admin panel page from URL address.

		$current  = WizyTowka\AdminPanel::getControllerClass();
		$expected = self::$_examplePageClass;
		$this->assertEquals($expected, $current);
	}

	/**
	 * @expectedException     WizyTowka\AdminPanelException
	 * @expectedExceptionCode 1
	 */
	public function testRegisterPageNameAlreadyRegistered()
	{
		WizyTowka\AdminPanel::registerPage(self::$_examplePageName, self::$_examplePageClass);
	}
}