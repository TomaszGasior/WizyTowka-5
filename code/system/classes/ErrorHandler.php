<?php

/**
* WizyTówka 5
* Errors handler. Manages error log, shows error page and converts PHP system errors to exceptions.
*/
namespace WizyTowka;

trait ErrorHandler
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

	// Warning: because of changes in PHP 7 we must not use type hint in $exception argument to keep backward compatibility with PHP 5.6.
	// More informations here: http://php.net/manual/en/migration70.incompatible.php#migration70.incompatible.error-handling.set-exception-handler

	static public function handleException($exception)  // For set_exception_handler().
	{
		self::_addToLog($exception);

		$isPlainText = !empty(array_filter(headers_list(), function($value){
			return stripos($value, 'content-type')!== false and stripos($value, 'text/html')===false;
			// Use plain text format for error message instead HTML, when 'content-type' HTTP header is set, but not contain 'text/html'.
		}));
		(PHP_SAPI == 'cli' or $isPlainText) ? self::_printAsPlainText($exception) : self::_printAsHTML($exception);
	}

	static public function handleError($number, $message, $file, $line)  // For set_error_handler().
	{
		if (error_reporting() !== 0) {  // Ignore error if @ operator was used.
			throw new \ErrorException($message, 0, $number, $file, $line);
		}
	}

	static private function _addToLog($exception)
	{
		// Error should not be logged, when ErrorHandler is ran outside normal CMS mode (in unit tests, in utility scripts or when CMS is not installed).
		if (!defined(__NAMESPACE__.'\\INIT') or !file_exists(CONFIG_DIR)) {
			return;
		}
		file_put_contents(
			CONFIG_DIR . '/errors.log', (
				"\n" . date('Y-m-d H:i') . '  ~~~~~~~~~~~~~~~~~~~~~~~~~~' .
				"\nType:    " . ( ($exception instanceof \ErrorException)
					? self::_getPHPErrorName($exception->getSeverity())
					: get_class($exception) . ((empty($exception->getCode()))?'':' #'.$exception->getCode())
				) .
				"\nMessage: " . $exception->getMessage() .
				"\nFile:    " . $exception->getFile() .
				"\nLine:    " . $exception->getLine() .
				"\nTrace: \n" . $exception->getTraceAsString() . "\n\n"
			), FILE_APPEND
		);
	}

	static private function _printAsPlainText($exception)
	{
		echo "\n\n", 'System encountered fatal error and execution must be interrupted.', "\n",
			"\nType:    " . ( ($exception instanceof \ErrorException)
				? self::_getPHPErrorName($exception->getSeverity())
				: get_class($exception) . ((empty($exception->getCode()))?'':' #'.$exception->getCode())
			),
			"\nMessage: ", $exception->getMessage(),
			"\nFile:    ", $exception->getFile(),
			"\nLine:    ", $exception->getLine(),
			"\n\nTrace:\n", $exception->getTraceAsString(), "\n\n";
	}

	static private function _printAsHTML($exception)
	{
		?><!doctype html><meta charset="utf-8"><?php

		// Show full error information, when error occurs outside normal CMS mode or when detailed errors are enabled.
		// Otherwise print only "sad face" message.
		if (!defined(__NAMESPACE__.'\\INIT') or !file_exists(CONFIG_DIR) or Settings::get('systemShowErrors')) {
			?>
			<style>
				html, body { margin: 0; padding: 0; height: 100%; width: 100%; }
				div.wtFaErr { color: #000; font: 16px /1.5em sans-serif; display: table; height: 100%; width: 100%; background: rgba( 0,0,0, 0.3 ); }
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
				<p>System encountered fatal error and execution must be interrupted.</p>
				<dl>
					<dt>Type</dt>
					<dd><?= ( ($exception instanceof \ErrorException)
							? self::_getPHPErrorName($exception->getSeverity())
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
			</section></div></div>
			<?php
		}
		else {
			?>
			<style>
				html, body { margin: 0; padding: 0; height: 100%; width: 100%; }
				div.wtHiErr { color: #777; font: 30px sans-serif; display: table; width: 100%; height: 100%; }
				div.wtHiErr > div { display: table-cell; vertical-align: middle; text-align: center; }
				div.wtHiErr span { display: block; font-size: 300px; color: #000; }
				@media (max-height: 500px), (max-width: 400px) {
					div.wtHiErr span { font-size: 200px; }
				}
			</style>
			<div class="wtHiErr"><div>
				<p><span role="presentation">&#9785;</span>Przepraszamy za usterki.</p>
				<!-- <?= get_class($exception) . ($exception->getCode() ? ' #'.$exception->getCode() : '') ?> -->
			</div></div>
			<?php
		}
	}

	static private function _getPHPErrorName($code)
	{
		return array_flip(
			array_filter(get_defined_constants(), function($key){ return $key[0].$key[1] == 'E_'; }, ARRAY_FILTER_USE_KEY)
		)[$code];
	}
}