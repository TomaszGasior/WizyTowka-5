<?php

/**
* WizyTówka 5 — unit test
*/
class ErrorHandlerTest extends PHPUnit\Framework\TestCase
{
	static private $_errorLogPath;

	static public function setUpBeforeClass()
	{
		self::$_errorLogPath = getcwd() . '/' . WizyTowka\CONFIG_DIR . '/errors.log';

		if (file_exists(self::$_errorLogPath)) {
			rename(self::$_errorLogPath, self::$_errorLogPath.'.bak');
		}
	}

	static public function tearDownAfterClass()
	{
		if (file_exists(self::$_errorLogPath)) {
			unlink(self::$_errorLogPath);
		}
		if (file_exists(self::$_errorLogPath.'.bak')) {
			rename(self::$_errorLogPath.'.bak', self::$_errorLogPath);
		}
	}

	public function testErrorHandler()
	{
		$exceptionMessage = 'Example exception #' . rand(100,999);
		$this->expectOutputRegex('/'.$exceptionMessage.'/');

		WizyTowka\ErrorHandler::handleException(new Exception($exceptionMessage, 8));

		$errorLog = file_get_contents(self::$_errorLogPath);
		$this->assertContains($exceptionMessage, $errorLog);
	}

	/**
	* @expectedException        ErrorException
	* @expectedExceptionMessage Example error
	*/
	public function testErrorsConverting()
	{
		WizyTowka\ErrorHandler::handleError(E_WARNING, 'Example error', 'examplefile.php', 1);
	}
}