<?php

/**
* WizyTówka 5 — unit test
*/
namespace WizyTowka
{
	function setcookie($name, $value, $expire)
	{
		\UserSessionTest::$_lastFakeCookie = compact('name', 'value', 'expire');
	}
	// Dirty hack. It is needed to check whether HTTP cookie is created properly, but cookies do not work in CLI scripts.
	// This function overwrites built-in PHP setcookie() in CMS namespace and puts information about cookie in test class.
}
namespace
{
	class UserSessionTest extends PHPUnit\Framework\TestCase
	{
		static private $_sessionsConfigFile;
		static public $_lastFakeCookie;  // Fake setcookie() function puts cookie data here.

		static public function setUpBeforeClass()
		{
			// Prepare session configuration file for test only.
			self::$_sessionsConfigFile = CONFIG_DIR . '/sessions.conf';
			if (file_exists(self::$_sessionsConfigFile)) {
				rename(self::$_sessionsConfigFile, self::$_sessionsConfigFile.'.bak');
			}
			WizyTowka\ConfigurationFile::createNew(self::$_sessionsConfigFile);

			// Set fake IP address in $_SERVER array.
			$_SERVER['REMOTE_ADDR'] = '127.0.0.1';

			// Run user session manager.
			WizyTowka\UserSession::setup();
		}

		static public function tearDownAfterClass()
		{
			if (file_exists(self::$_sessionsConfigFile)) {
				unlink(self::$_sessionsConfigFile);
			}
			if (file_exists(self::$_sessionsConfigFile.'.bak')) {
				rename(self::$_sessionsConfigFile.'.bak', self::$_sessionsConfigFile);
			}
		}

		public function setUp()
		{
			// Restore cookie created by fake setcookie() function declarated on the top of this file.
			if (self::$_lastFakeCookie) {
				$_COOKIE[self::$_lastFakeCookie['name']] = self::$_lastFakeCookie['value'];
			}
		}

		public function testLogIn()
		{
			$userId = 678;
			$sessionDuration = 3600;  // Seconds.

			WizyTowka\UserSession::logIn($userId, $sessionDuration);

			$cookieExpireTime = self::$_lastFakeCookie['expire'];
			$this->assertEquals(time()+$sessionDuration, $cookieExpireTime);

			$sessionId = self::$_lastFakeCookie['value'];
			$sessionsConfigFile = new WizyTowka\ConfigurationFile(self::$_sessionsConfigFile);
			$this->assertTrue(isset($sessionsConfigFile->$sessionId));
			$this->assertEquals($userId, $sessionsConfigFile->$sessionId['userId']);

			$this->assertTrue(WizyTowka\UserSession::isLoggedIn());

			$this->assertEquals($userId, WizyTowka\UserSession::getUserId());
		}

		public function testLogOut()
		{
			$sessionId = self::$_lastFakeCookie['value']; // From previous setcookie() call.

			WizyTowka\UserSession::logOut();

			$cookieExpireTime = self::$_lastFakeCookie['expire'];
			$this->assertTrue($cookieExpireTime < time());

			$sessionsConfigFile = new WizyTowka\ConfigurationFile(self::$_sessionsConfigFile);
			$this->assertFalse(isset($sessionsConfigFile->$sessionId));

			$this->assertFalse(WizyTowka\UserSession::isLoggedIn());

			$this->assertFalse(WizyTowka\UserSession::getUserId());
		}
	}
}