<?php

/**
* WizyTówka 5 — unit test
*/
namespace WizyTowka\UnitTests;
use WizyTowka as __;

class SessionManagerTest extends TestCase
{
	private const INSTANCE_COOKIE_NAME = 'ExampleSessionInstance';
	private const INSTANCE_CONFIG_FILE = TEMP_FILES_DIR . '/SessionManager_sessions.conf';

	private const EXAMPLE_USER_ID          = 678;
	private const EXAMPLE_SESSION_ID       = '52e91938219800668c14859e756cfc87c61c92f2aeeafe412444455af9c4e3a9b8d2cfcac84f86ad2f55e88c266a67f7db86e1112e2f46ee13fe968b3ec8acb6';
	private const EXAMPLE_SESSION_DURATION = 3600;

	public function setUp() : void
	{
		// $_SERVER values undefined in CLI.
		$_SERVER['REMOTE_ADDR']     = '127.0.0.1';
		$_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (X11; Linux x86_64; rv:54.0) Gecko/20100101 Firefox/54.0';
	}

	public function tearDown() : void
	{
		unlink(self::INSTANCE_CONFIG_FILE);
	}

	// Prepares sessions config file and HTTP cookie needed by SessionManager.
	private function _prepareFakeSession()
	{
		$sessionsConfigFile = <<< 'JSON'
{
    "%s": {
        "userId": 678,
        "waiString": "1c4a5c73a08b4311959b415124e25fe3859249ef5a7ff5c946fb0bdca614832b0469f9315d1744fc47af96199c9390e34da48bdfcd75e0eab2363c45b7ac67f6",
        "expireTime": %d,
        "reloginTime": %d
    }
}
JSON;
		file_put_contents(
			self::INSTANCE_CONFIG_FILE,
			sprintf(
				$sessionsConfigFile,
				self::EXAMPLE_SESSION_ID, time() + self::EXAMPLE_SESSION_DURATION, time() + 120
			)
		);

		$_COOKIE[self::INSTANCE_COOKIE_NAME] = self::EXAMPLE_SESSION_ID;
	}

	/**
	* @runInSeparateProcess
	*/
	public function testLogIn() : void
	{
		__\ConfigurationFile::createNew(self::INSTANCE_CONFIG_FILE);
		$sessionsConfigFile = new __\ConfigurationFile(self::INSTANCE_CONFIG_FILE);

		$sessionManager = new __\_Private\SessionManager(self::INSTANCE_COOKIE_NAME, $sessionsConfigFile);

		$sessionManager->logIn(self::EXAMPLE_USER_ID, self::EXAMPLE_SESSION_DURATION);

		// Check expire time of session cookie.
		$current  = $this->getLastHTTPCookie()['expires'];
		$expected = time() + self::EXAMPLE_SESSION_DURATION;
		$this->assertEquals($expected, $current);

		// Check cookie name.
		$current  = $this->getLastHTTPCookie()['name'];
		$expected = self::INSTANCE_COOKIE_NAME;
		$this->assertEquals($expected, $current);

		$sessionId = $this->getLastHTTPCookie()['value'];

		// Check session data in sessions configuration file.
		$current  = $sessionsConfigFile->$sessionId['userId'];
		$expected = self::EXAMPLE_USER_ID;
		$this->assertEquals($expected, $current);
	}

	/**
	* @runInSeparateProcess
	*/
	public function testIsUserLoggedIn() : void
	{
		$this->_prepareFakeSession();
		$sessionsConfigFile = new __\ConfigurationFile(self::INSTANCE_CONFIG_FILE);

		$sessionManager = new __\_Private\SessionManager(self::INSTANCE_COOKIE_NAME, $sessionsConfigFile);

		// Check current user ID.
		$current  = $sessionManager->getUserId();
		$expected = self::EXAMPLE_USER_ID;
		$this->assertEquals($expected, $current);

		$this->assertTrue($sessionManager->isUserLoggedIn());
	}

	/**
	* @runInSeparateProcess
	*/
	public function testLogOut() : void
	{
		$this->_prepareFakeSession();
		$sessionsConfigFile = new __\ConfigurationFile(self::INSTANCE_CONFIG_FILE);

		$sessionManager = new __\_Private\SessionManager(self::INSTANCE_COOKIE_NAME, $sessionsConfigFile);

		$sessionManager->logOut();

		// Check whether session data was removed from sessions configuration file.
		$this->assertFalse(isset($sessionsConfigFile->{self::EXAMPLE_SESSION_ID}));

		// Check whether session ID was removed from cookie.
		$current     = $this->getLastHTTPCookie()['value'];
		$notExpected = self::EXAMPLE_SESSION_ID;
		$this->assertNotEquals($notExpected, $current);

		// Check whether cookie is expired.
		$cookieExpireTime = $this->getLastHTTPCookie()['expires'];
		$this->assertTrue($cookieExpireTime < time());

		// Current data in SessionManager. User should be corretly logged out immediately.
		$this->assertNull($sessionManager->getUserId());
		$this->assertFalse($sessionManager->isUserLoggedIn());
	}
}