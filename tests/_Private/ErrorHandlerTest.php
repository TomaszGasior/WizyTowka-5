<?php

/**
* WizyTówka 5 — unit test
*/
namespace WizyTowka\UnitTests;
use WizyTowka as __;

class ErrorHandlerTest extends TestCase
{
	static private $_errorLogPath = __\CONFIG_DIR . '/errors_test.log';

	public function tearDown()
	{
		@unlink(self::$_errorLogPath);
	}

	public function testErrorHandler()
	{
		$errorHandler = new __\_Private\ErrorHandler;
		$errorHandler->setLogFilePath(self::$_errorLogPath);

		$exceptionMessage = 'Example exception #' . rand(100,999);
		$this->expectOutputRegex('/'.$exceptionMessage.'/');

		$errorHandler->handleException(new \Exception($exceptionMessage, 8));

		$this->assertRegExp('/'.$exceptionMessage.'/', file_get_contents(self::$_errorLogPath));
	}

	public function testAddToLog()
	{
		$errorHandler = new __\_Private\ErrorHandler;
		$errorHandler->setLogFilePath(self::$_errorLogPath);

		$exceptionMessage = 'Other example exception #' . rand(100,999);
		$errorHandler->addToLog(new \Exception($exceptionMessage, 8));

		$this->assertRegExp('/'.$exceptionMessage.'/', file_get_contents(self::$_errorLogPath));
	}

	/**
	* @expectedException        ErrorException
	* @expectedExceptionMessage Example error
	*/
	public function testErrorsConverting()
	{
		$errorHandler = new __\_Private\ErrorHandler;

		$errorHandler->handleError(E_WARNING, 'Example error', 'examplefile.php', 1);
	}
}