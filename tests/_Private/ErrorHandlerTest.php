<?php

/**
* WizyTówka 5 — unit test
*/
class ErrorHandlerTest extends TestCase
{
	static private $_errorLogPath = WizyTowka\CONFIG_DIR . '/errors_test.log';

	static public function tearDownAfterClass()
	{
		@unlink(self::$_errorLogPath);
	}

	public function testErrorHandler()
	{
		$errorHandler = new WizyTowka\_Private\ErrorHandler(self::$_errorLogPath);

		$exceptionMessage = 'Example exception #' . rand(100,999);
		$this->expectOutputRegex('/'.$exceptionMessage.'/');

		$errorHandler->handleException(new Exception($exceptionMessage, 8));

		$this->assertRegExp('/'.$exceptionMessage.'/', file_get_contents(self::$_errorLogPath));
	}

	public function testAddToLog()
	{
		$errorHandler = new WizyTowka\_Private\ErrorHandler(self::$_errorLogPath);

		$exceptionMessage = 'Other example exception #' . rand(100,999);
		$errorHandler->addToLog(new Exception($exceptionMessage, 8));

		$this->assertRegExp('/'.$exceptionMessage.'/', file_get_contents(self::$_errorLogPath));
	}

	/**
	* @expectedException        ErrorException
	* @expectedExceptionMessage Example error
	*/
	public function testErrorsConverting()
	{
		$errorHandler = new WizyTowka\_Private\ErrorHandler(self::$_errorLogPath);

		$errorHandler->handleError(E_WARNING, 'Example error', 'examplefile.php', 1);
	}
}