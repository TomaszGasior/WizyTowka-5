<?php

/**
* WizyTówka 5 — unit test
*/
namespace WizyTowka\UnitTests;
use WizyTowka as __;

class AdminPanelTest extends TestCase
{
	static private $_examplePageName = 'example';
	static private $_examplePageClass;

	static public function setUpBeforeClass()
	{
		// Prepare fake session manager.
		$fakeSessionManager = new class()
		{
			public function isUserLoggedIn()
			{
				return false;
			}
		};
		__\WT()->overwrite('session', $fakeSessionManager);

		self::$_examplePageClass = get_class(new class() extends __\AdminPanelPage
		{
			// It's needed to run test. Otherwise AdminPanelPage's constructor redirects to login screen.
			protected $_userMustBeLoggedIn = false;

			public function showMessage()
			{
				echo 'Everything works fine!';
			}
		});
	}

	public function testRegisterPage()
	{
		__\AdminPanel::registerPage(self::$_examplePageName, self::$_examplePageClass);

		$_GET['c'] = self::$_examplePageName;
		// AdminPanel reads name of admin panel page from "c" parameter of URL address.

		// AdminPanel isn't real controller of admin panel. It works as proxy for real class of
		// proper admin panel page controller, which inherits from AdminPanelPage class (not AdminPanel).
		$this->expectOutputString('Everything works fine!');
		(new __\AdminPanel)->showMessage();
	}

	/**
	 * @expectedException     WizyTowka\AdminPanelException
	 * @expectedExceptionCode 1
	 */
	public function testRegisterPageNameAlreadyRegistered()
	{
		__\AdminPanel::registerPage(self::$_examplePageName, self::$_examplePageClass);
	}
}