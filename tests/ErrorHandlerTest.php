<?php

/**
* WizyTówka 5 — unit test
*/
class ErrorHandlerTest extends TestCase
{
	static private $_errorLogPath = WizyTowka\CONFIG_DIR . '/errors.log';

	static public function setUpBeforeClass()
	{
		@rename(self::$_errorLogPath, self::$_errorLogPath.'.bak');
	}

	static public function tearDownAfterClass()
	{
		@rename(self::$_errorLogPath.'.bak', self::$_errorLogPath);
	}

	public function testErrorHandler()
	{
		$exceptionMessage = 'Example exception #' . rand(100,999);
		$this->expectOutputRegex('/'.$exceptionMessage.'/');

		WizyTowka\ErrorHandler::handleException(new Exception($exceptionMessage, 8));

		$this->assertRegExp('/'.$exceptionMessage.'/', file_get_contents(self::$_errorLogPath));
	}

	public function testAddToLog()
	{
		$exceptionMessage = 'Other example exception #' . rand(100,999);
		WizyTowka\ErrorHandler::addToLog(new Exception($exceptionMessage, 8));

		$this->assertRegExp('/'.$exceptionMessage.'/', file_get_contents(self::$_errorLogPath));
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