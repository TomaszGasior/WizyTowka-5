<?php

/**
* WizyTówka 5
* Errors handler. Manages error log, shows error page and converts PHP system errors.
*/
namespace WizyTowka\_Private;
use WizyTowka as __;

class ErrorHandler
{
	private $_showDetails = true;
	private $_logFilePath;

	public function handleError(int $number, string $message, string $file, int $line) : void  // For set_error_handler().
	{
		if (error_reporting() !== 0) {   // Ignore error if @ operator was used.
			throw new \ErrorException($message, 0, $number, $file, $line);
		}
	}

	public function handleException(\Throwable $exception) : void  // For set_exception_handler().
	{
		$this->addToLog($exception);

		// Use plain text format for error message instead HTML, when 'content-type' HTTP header does not contain 'text/html'.
		$isPlainText = !empty(array_filter(
			headers_list(),
			function($value){ return stripos($value, 'content-type:') !== false and stripos($value, 'text/html') === false; }
		));

		if (PHP_SAPI == 'cli' or $isPlainText) {
			$this->_printAsPlainText($exception);
		}
		else {
			@header('HTTP/1.1 500 Internal Server Error');
			$this->_showDetails ? $this->_printAsHTML($exception) : $this->_printAsQuietHTML($exception);
		}
	}

	public function addToLog(\Throwable $exception) : bool
	{
		if ($this->_logFilePath) {
			$result = @file_put_contents(
				$this->_logFilePath,
				date('Y-m-d H:i') . '  ~~~~~~~~~~~~~~~~~~~~~~~~~~' . "\n" . $this->_prepareMessage($exception) . "\n\n\n" ,
				FILE_APPEND
			);

			return $result !== false;
		}

		return false;
	}

	public function setShowDetails(bool $setting) : void
	{
		$this->_showDetails = $setting;
	}

	public function getShowDetails() : bool
	{
		return $this->_showDetails;
	}

	public function setLogFilePath(?string $logFilePath) : void
	{
		$this->_logFilePath = $logFilePath;
	}

	public function getLogFilePath() : ?string
	{
		return $this->_logFilePath;
	}

	private function _printAsPlainText(\Throwable $exception) : void
	{
		echo "\n\n", 'System encountered fatal error and execution must be interrupted.', "\n\n",
		     $this->_prepareMessage($exception), "\n\n";
	}

	private function _printAsHTML(\Throwable $exception) : void
	{
		?><!doctype html><meta charset="utf-8"><meta name="viewport" content="width=device-width">
<style>
	div.wtErr { display: flex; align-items: center; justify-content: center; position: fixed; left: 0; right: 0; top: 0; bottom: 0; background: rgba(0,0,0, 0.3); z-index: 9999; }
	div.wtErr section { color: #000; font: 15px /1.5em sans-serif; border: 2px solid #f00; padding: 0.4em 1em; background: #fff; box-shadow: 0 0 9px rgba(0,0,0, 0.4); max-width: 700px; min-width: 0; }
	div.wtErr h1 { font-size: 1.7em; margin: 0.7em 0 -0.4em; color: #f00; }
	div.wtErr pre { letter-spacing: -1px; white-space: pre-wrap; overflow-y: auto; max-height: 400px; }
	@media (max-height: 500px), (max-width: 400px) { div.wtErr { align-items: stretch; } }
</style>
<div class="wtErr"><section>
	<h1>Błąd krytyczny — WizyTówka <?= __\VERSION ?></h1>
	<p>Działanie systemu WizyTówka zostało przerwane z powodu krytycznego błędu.</p>
	<pre><?= htmlspecialchars($this->_prepareMessage($exception)) ?></pre>
</section></div>
		<?php
	}

	private function _printAsQuietHTML(\Throwable $exception) : void
	{
		?><!doctype html><meta charset="utf-8"><meta name="viewport" content="width=device-width">
<style>
	div.wtQErr { display: flex; align-items: center; justify-content: center; position: fixed; left: 0; right: 0; top: 0; bottom: 0; }
	div.wtQErr p { color: #777; font: 30px sans-serif; text-align: center; }
	div.wtQErr span { display: block; font-size: 300px; color: #000; }
	@media (max-height: 500px), (max-width: 400px) { div.wtQErr span { font-size: 200px; } }
</style>
<div class="wtQErr">
	<p><span role="presentation">&#9785;</span>Przepraszamy za usterki.</p>
	<!-- <?= $this->_prepareName($exception) ?> -->
</div>
		<?php
	}

	private function _prepareName(\Throwable $exception) : string
	{
		$getPHPErrorName = function($code)
		{
			return array_flip(
				array_filter(get_defined_constants(), function($key){ return $key[0].$key[1] == 'E_'; }, ARRAY_FILTER_USE_KEY)
			)[$code];
		};

		return ($exception instanceof \ErrorException)
		       ? $getPHPErrorName($exception->getSeverity())
		       : get_class($exception) . ($exception->getCode() ? ' #'.$exception->getCode() : '');
	}

	private function _prepareMessage(\Throwable $exception) : string
	{
		$exceptionType = $this->_prepareName($exception);

		return <<< TXT
Type:    $exceptionType
Message: {$exception->getMessage()}
File:    {$exception->getFile()}
Line:    {$exception->getLine()}
Trace:
{$exception->getTraceAsString()}
TXT;
	}
}