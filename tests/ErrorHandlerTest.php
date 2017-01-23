<?php

/**
* WizyTówka 5 — unit test
*/
class ErrorHandlerTest extends PHPUnit\Framework\TestCase
{
	static private $_errorLogPath;

	public function testErrorHandler()
	{
		$exceptionMessage = 'Example exception #' . rand(100,999);
		$this->expectOutputRegex('/'.$exceptionMessage.'/');

		WizyTowka\ErrorHandler::handleException(new Exception($exceptionMessage, 8));
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