<?php

/**
* WizyTówka 5
* Errors handler. Manages error log, shows error page and converts PHP system errors.
*/
namespace WizyTowka;

trait ErrorHandler
{
	// Warning: \Throwable type hint in $exception argument must not be used to keep backward compatibility with PHP 5.6.
	// More here: http://php.net/manual/en/migration70.incompatible.php#migration70.incompatible.error-handling.set-exception-handler

	static private $_showErrorDetails = true;

	static public function handleError($number, $message, $file, $line)  // For set_error_handler().
	{
		if (error_reporting() !== 0) {   // Ignore error if @ operator was used.
			throw new \ErrorException($message, 0, $number, $file, $line);
		}
	}

	static public function handleException(/*\Throwable*/ $exception)  // For set_exception_handler().
	{
		self::addToLog($exception);

		// Use plain text format for error message instead HTML, when 'content-type' HTTP header don't contain 'text/html'.
		$isPlainText = !empty(array_filter(
			headers_list(),
			function($value){ return stripos($value, 'content-type') !== false and stripos($value, 'text/html') === false; }
		));

		(PHP_SAPI == 'cli' or $isPlainText)
		? self::_printAsPlainText($exception)
		: (self::$_showErrorDetails ? self::_printAsHTML($exception) : self::_printAsQuietHTML($exception));
	}

	static public function addToLog(/*\Throwable*/ $exception)
	{
		@file_put_contents(
			CONFIG_DIR . '/errors.log',
			date('Y-m-d H:i') . '  ~~~~~~~~~~~~~~~~~~~~~~~~~~' . "\n" . self::_prepareMessage($exception) . "\n\n\n" ,
			FILE_APPEND
		);
	}

	static public function showErrorDetails($setting = null)
	{
		if ($setting === null) {
			return self::$_showErrorDetails;
		}
		self::$_showErrorDetails = (bool)$setting;
	}

	static private function _printAsPlainText(/*\Throwable*/ $exception)
	{
		echo "\n\n", 'System encountered fatal error and execution must be interrupted.', "\n\n",
		     self::_prepareMessage($exception), "\n\n";
	}

	static private function _printAsHTML(/*\Throwable*/ $exception)
	{
		?><!doctype html><meta charset="utf-8">
<style>
	div.wtErr { display: flex; align-items: center; justify-content: center; position: fixed; left: 0; right: 0; top: 0; bottom: 0; background: rgba(0,0,0, 0.3); }
	div.wtErr section { color: #000; font: 16px /1.5em sans-serif; border: 1px solid #f00; padding: 0.4em 1em; background: #fff; box-shadow: 0 0 9px rgba(0,0,0, 0.4); max-width: 700px; min-width: 0; }
	div.wtErr h1 { font-size: 1.7em; margin-bottom: -0.4em; color: #f00; }
	div.wtErr pre { letter-spacing: -1px; white-space: pre-wrap; overflow-y: auto; max-height: 215px; }
</style>
<div class="wtErr"><section>
	<h1>Błąd krytyczny — WizyTówka <?= VERSION ?></h1>
	<p>Działanie systemu WizyTówka zostało przerwane z powodu krytycznego błędu.</p>
	<pre><?= htmlspecialchars(self::_prepareMessage($exception)) ?></pre>
</section></div>
		<?php
	}

	static private function _printAsQuietHTML(/*\Throwable*/ $exception)
	{
		?><!doctype html><meta charset="utf-8">
<style>
	div.wtQErr { display: flex; align-items: center; justify-content: center; position: fixed; left: 0; right: 0; top: 0; bottom: 0; }
	div.wtQErr p { color: #777; font: 30px sans-serif; text-align: center; }
	div.wtQErr span { display: block; font-size: 300px; color: #000; }
	@media (max-height: 500px), (max-width: 400px) { div.wtQErr span { font-size: 200px; } }
</style>
<div class="wtQErr">
	<p><span role="presentation">&#9785;</span>Przepraszamy za usterki.</p>
	<!-- <?= self::_prepareName($exception) ?> -->
</div>
		<?php
	}

	static private function _prepareName(/*\Throwable*/ $exception)
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

	static private function _prepareMessage(/*\Throwable*/ $exception)
	{
		$exceptionType = self::_prepareName($exception);

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