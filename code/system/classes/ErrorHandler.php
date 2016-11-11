<?php

/**
* WizyTówka 5
* Errors handler. Manages error log, shows error page and converts PHP system errors to exceptions.
*/
namespace WizyTowka;

class ErrorHandler
{
	static private $_namedPHPErrors = [
		2     => 'E_WARNING',
		8     => 'E_NOTICE',
		256   => 'E_USER_ERROR',
		512   => 'E_USER_WARNING',
		1024  => 'E_USER_NOTICE',
		2048  => 'E_STRICT',
		4096  => 'E_RECOVERABLE_ERROR',
		8192  => 'E_DEPRECATED',
		16384 => 'E_USER_DEPRECATED'
	];

	// Warning: because of changes in PHP 7 we must not use type hint in $exception method argument to keep backward compatibility with older PHP versions.
	// More informations here: http://php.net/manual/en/function.set-exception-handler.php

	static public function handleException($exception)  // For set_exception_handler().
	{
		self::addToLog($exception);

		$isPlainText = !empty(array_filter(headers_list(), function($value){
			return stripos($value, 'content-type')!== false and stripos($value, 'text/html')===false;
			// Use plain text format for error message instead HTML, when 'content-type' HTTP header is set, but not contain 'text/html'.
		}));
		(PHP_SAPI == 'cli' or $isPlainText) ? self::printAsPlainText($exception) : self::printAsHTML($exception);
	}

	static public function convertErrorToException($number, $message, $file, $line)  // For set_error_handler().
	{
		if (error_reporting() !== 0) { // Ignore error if @ operator was used.
			throw new \ErrorException($message, 0, $number, $file, $line);
		}
	}

	static private function addToLog($exception)
	{
		if (!defined('CONFIG_DIR')) {
			return;
			// CONFIG_DIR can be not defined when ErrorHandler is ran outside normal CMS code (in tests or utility scripts).
		}

		file_put_contents(
			CONFIG_DIR . '/errors.log', (
				"\n\n\n" . date('Y-m-d H:i') . '  ~~~~~~~~~~~~~~~~~~~~~~~~~~' .
				"\nType:    " . ( ($exception instanceof \ErrorException)
					? self::$_namedPHPErrors[$exception->getSeverity()]
					: get_class($exception) . ((empty($exception->getCode()))?'':' #'.$exception->getCode())
				) .
				"\nMessage: " . $exception->getMessage() .
				"\nFile:    " . $exception->getFile() .
				"\nLine:    " . $exception->getLine() .
				"\nTrace: \n" . $exception->getTraceAsString()
			), FILE_APPEND
		);
	}

	static private function printAsPlainText($exception)
	{
		echo "\n\n", 'System encountered fatal error and executing must be interrupted.', "\n",
			"\nType:    " . ( ($exception instanceof \ErrorException)
				? self::$_namedPHPErrors[$exception->getSeverity()]
				: get_class($exception) . ((empty($exception->getCode()))?'':' #'.$exception->getCode())
			),
			"\nMessage: ", $exception->getMessage(),
			"\nFile:    ", $exception->getFile(),
			"\nLine:    ", $exception->getLine(),
			"\n\nTrace:\n", $exception->getTraceAsString(), "\n\n";
	}

	static private function printAsHTML($exception)
	{
		?><!doctype html><meta charset="utf-8"><title>Fatal error</title>
<style>
	div.wtFaErr { color: #000; font: 16px /1.5em sans-serif; position: fixed; left: 0; right: 0; top: 0; bottom: 0; display: table; height: 100%; width: 100%; background: rgba( 0,0,0, 0.3 ); }
	div.wtFaErr > div { display: table-cell; vertical-align: middle; }
	div.wtFaErr section { border: 1px solid red; max-width: 700px; margin: auto; padding: 0.4em 1em; background: #fff; box-shadow: 0 0 9px rgba( 0,0,0, 0.4 ); }
	div.wtFaErr h1 { font-size: 1.7em; margin-bottom: -0.4em; color: #ff0000; }
	div.wtFaErr dt { float: left; clear: both; width: 20%; opacity: 0.5; }
	div.wtFaErr dd { float: left; width: 80%; margin-left: 0; }
	div.wtFaErr dt:last-of-type { display: none; }
	div.wtFaErr dd:last-of-type { clear: both; float: none; width: 100%; padding-top: 0.4em; }
	div.wtFaErr #trace:not(:target), div.wtFaErr #trace:target + a { display: none; }
	div.wtFaErr a { padding: 0.1em 1.5em; background: #eee; color: inherit; }
</style>
<div class="wtFaErr"><div><section>
	<h1>Fatal error — WizyTówka <?= VERSION ?></h1>
	<p>System encountered fatal error and executing must be interrupted.</p>
	<dl>
		<dt>Type</dt>
		<dd><?= ( ($exception instanceof \ErrorException)
				? self::$_namedPHPErrors[$exception->getSeverity()]
				: get_class($exception) . ((empty($exception->getCode()))?'':' #'.$exception->getCode())
			) ?></dd>
		<dt>Message</dt>
		<dd><?= htmlspecialchars($exception->getMessage()) ?></dd>
		<dt>File</dt>
		<dd><?= $exception->getFile() ?></dd>
		<dt>Line</dt>
		<dd><?= $exception->getLine() ?></dd>
		<dt>Trace</dt>
		<dd>
			<span id="trace"><?= nl2br(htmlspecialchars($exception->getTraceAsString())) ?></span>
			<a href="#trace">Show trace…</a>
		</dd>
	</dl>
</section></div></div><?php
	}
}