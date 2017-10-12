<?php

/**
* WizyTówka 5 — unit test
*/
// Workarounds: overwritten setcookie() PHP function.

class SessionManagerTest extends PHPUnit\Framework\TestCase
{
	static private $_sessionsConfigFile = WizyTowka\CONFIG_DIR . '/sessions.conf';

	static public function setUpBeforeClass()
	{
		// Prepare session configuration file for test only.
		@rename(self::$_sessionsConfigFile, self::$_sessionsConfigFile.'.bak');
		WizyTowka\ConfigurationFile::createNew(self::$_sessionsConfigFile);

		// Run user session manager.
		WizyTowka\SessionManager::setup();
	}

	static public function tearDownAfterClass()
	{
		@unlink(self::$_sessionsConfigFile);
		@rename(self::$_sessionsConfigFile.'.bak', self::$_sessionsConfigFile);
	}

	public function testLogIn()
	{
		$exampleUserId = 678;
		$exampleSessionDuration = 3600;

		WizyTowka\SessionManager::logIn($exampleUserId, $exampleSessionDuration);

		$sessionId          = workaroundsData()->lastCookie['value'];  // See workarounds.php.
		$sessionsConfigFile = new WizyTowka\ConfigurationFile(self::$_sessionsConfigFile);
		$cookieExpireTime   = workaroundsData()->lastCookie['expire'];  // See workarounds.php.

		// Session data in sessions configuration file.
		$this->assertTrue(isset($sessionsConfigFile->$sessionId));

		// Session ID in session data from configuration file.
		$current  = $sessionsConfigFile->$sessionId['userId'];
		$expected = $exampleUserId;
		$this->assertEquals($expected, $current);

		// Expire time of session cookie.
		$current  = $cookieExpireTime;
		$expected = time()+$exampleSessionDuration;
		$this->assertEquals($expected, $current);

		// Current data in SessionManager trait.
		$this->assertTrue(
			WizyTowka\SessionManager::isUserLoggedIn()
		);
		$current  = WizyTowka\SessionManager::getUserId();
		$expected = $exampleUserId;
		$this->assertEquals($expected, $current);
	}

	public function testLogOut()
	{
		$sessionId = workaroundsData()->lastCookie['value'];  // From previous setcookie() call.

		WizyTowka\SessionManager::logOut();

		$sessionsConfigFile = new WizyTowka\ConfigurationFile(self::$_sessionsConfigFile);
		$cookieExpireTime   = workaroundsData()->lastCookie['expire'];  // See workarounds.php.

		// Session data in sessions configuration file.
		$this->assertFalse(isset($sessionsConfigFile->$sessionId));

		// Expire time of session cookie.
		$this->assertTrue($cookieExpireTime < time());

		// Current data in SessionManager trait.
		$this->assertFalse(WizyTowka\SessionManager::isUserLoggedIn());
		$this->assertFalse(WizyTowka\SessionManager::getUserId());
	}
}