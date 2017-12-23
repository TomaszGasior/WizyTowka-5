<?php

/**
* WizyTówka 5 — unit test
*/
class AdminPanelTest extends TestCase
{
	static private $_examplePageName = 'example';
	static private $_examplePageClass;

	static public function setUpBeforeClass()
	{
		self::$_examplePageClass = get_class(new class() extends WizyTowka\AdminPanelPage
		{
			// It's needed to run test. Without it AdminPanelPage's constructor runs Controller::redirect() method, which exits script.
			protected $_userMustBeLoggedIn = false;

			public function showMessage() {
				echo 'Everything works fine!';
			}
		});
	}

	public function testRegisterPage()
	{
		WizyTowka\AdminPanel::registerPage(self::$_examplePageName, self::$_examplePageClass);

		$_GET['c'] = self::$_examplePageName;
		// AdminPanel reads name of admin panel page from "c" parameter of URL address.

		// AdminPanel isn't real controller of admin panel. It works as proxy for real class of
		// proper admin panel page controller, which inherits from AdminPanelPage class (not AdminPanel).
		$this->expectOutputString('Everything works fine!');
		(new WizyTowka\AdminPanel)->showMessage();
	}

	/**
	 * @expectedException     WizyTowka\AdminPanelException
	 * @expectedExceptionCode 1
	 */
	public function ttestRegisterPageNameAlreadyRegistered()
	{
		WizyTowka\AdminPanel::registerPage(self::$_examplePageName, self::$_examplePageClass);
	}
}