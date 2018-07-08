<?php

/**
* WizyTówka 5 — unit test
*/
namespace WizyTowka\UnitTests;
use WizyTowka as __;

class AdminPanelTest extends TestCase
{
	private const EXAMPLE_PAGE_NAME = 'example';

	static private $_examplePageClass;

	static public function setUpBeforeClass() : void
	{
		// Fake session manager. isUserLoggedIn() is needed by AdminPanelPage's constructor.
		__\WT()->overwrite('session', new class()
		{
			public function isUserLoggedIn() : bool
			{
				return false;
			}
		});

		// Class of example admin panel page.
		self::$_examplePageClass = get_class(new class() extends __\AdminPanelPage
		{
			// It's needed to run test. Otherwise AdminPanelPage's constructor redirects to login screen.
			protected $_userMustBeLoggedIn = false;

			public function showMessage() : void
			{
				echo 'Everything works fine!';
			}
		});
	}

	static public function tearDownAfterClass() : void
	{
		__\WT()->overwrite('session', null);
	}

	public function testRegisterPage() : void
	{
		__\AdminPanel::registerPage(self::EXAMPLE_PAGE_NAME, self::$_examplePageClass);

		// AdminPanel reads name of admin panel page from "c" parameter of URL address.
		$_GET['c'] = self::EXAMPLE_PAGE_NAME;

		// AdminPanel isn't real controller of admin panel. It works as proxy for real class of
		// proper admin panel page controller, which inherits from AdminPanelPage class (not AdminPanel).
		$this->expectOutputString('Everything works fine!');
		(new __\AdminPanel)->showMessage();
	}

	/**
	* @expectedException     WizyTowka\AdminPanelException
	* @expectedExceptionCode 1
	*/
	public function testRegisterPageNameAlreadyRegistered() : void
	{
		__\AdminPanel::registerPage(self::EXAMPLE_PAGE_NAME, self::$_examplePageClass);
	}
}