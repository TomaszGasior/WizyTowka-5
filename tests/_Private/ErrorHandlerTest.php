<?php

/**
* WizyTówka 5 — unit test
*/
namespace WizyTowka\UnitTests;
use WizyTowka as __;

class ErrorHandlerTest extends TestCase
{
	private const LOG_FILE = TEMP_FILES_DIR . '/ErrorHandler_errors.log';

	static public function tearDownAfterClass() : void
	{
		unlink(self::LOG_FILE);
	}

	public function testErrorHandler() : void
	{
		$errorHandler = new __\_Private\ErrorHandler;
		$errorHandler->setLogFilePath(self::LOG_FILE);

		$exceptionMessage = 'Example exception #' . rand(100,999);
		$this->expectOutputRegex('/'.$exceptionMessage.'/');

		$errorHandler->handleException(new \Exception($exceptionMessage, 8));

		$this->assertRegExp('/'.$exceptionMessage.'/', file_get_contents(self::LOG_FILE));
	}

	public function testAddToLog() : void
	{
		$errorHandler = new __\_Private\ErrorHandler;
		$errorHandler->setLogFilePath(self::LOG_FILE);

		$exceptionMessage = 'Other example exception #' . rand(100,999);
		$errorHandler->addToLog(new \Exception($exceptionMessage, 8));

		$this->assertRegExp('/'.$exceptionMessage.'/', file_get_contents(self::LOG_FILE));
	}

	/**
	* @expectedException        ErrorException
	* @expectedExceptionMessage Example error
	*/
	public function testErrorsConverting() : void
	{
		$errorHandler = new __\_Private\ErrorHandler;

		$errorHandler->handleError(E_WARNING, 'Example error', 'examplefile.php', 1);
	}
}