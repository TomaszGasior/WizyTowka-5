<?php

/**
* WizyTówka 5
* This script reads CMS source and lists all exceptions contents and numbers.
*/
namespace WizyTowka;


const SYSTEM_DIR = __DIR__ . '/../code/system';
include SYSTEM_DIR . '/init.php';


echo 'WizyTówka ', VERSION, (VERSION_STABLE) ? '' : ' UNSTABLE', ' — system exceptions list', PHP_EOL, PHP_EOL;

function printException(array $exception)
{
	echo '#', str_pad($exception['code'], 3, ' ', STR_PAD_LEFT), ' — ', $exception['file'], PHP_EOL, '       ', $exception['message'], PHP_EOL;
}

$foundExceptions = [];
$classesFiles = glob(SYSTEM_DIR . '/classes/*.php');

foreach ($classesFiles as $filePath) {
	$fileLines = file($filePath, FILE_SKIP_EMPTY_LINES);

	foreach ($fileLines as $line) {
		if (strpos($line, 'throw new \Exception') !== false) {
			$exception = [];
			$exception['file'] = basename($filePath);

			$line = str_replace(['throw new \Exception(', 'throw new \Exception (', ');', ') ;'], null, trim($line));
			list($exception['code'], $exception['message']) = array_map('trim', array_map('strrev', explode(',', strrev($line), 2)));

			if (isset($foundExceptions[$exception['code']])) {
				echo 'WARNING: code ', $exception['code'], ' is duplicated in two exceptions!', PHP_EOL, PHP_EOL;
				printException($foundExceptions[$exception['code']]);
				printException($exception);
				exit;
			}

			$foundExceptions[$exception['code']] = $exception;
		}
	}
}

ksort($foundExceptions);

$exceptionsCodes = array_keys($foundExceptions);
$minCode = current($exceptionsCodes);
end($exceptionsCodes);
$maxCode = current($exceptionsCodes);

if ($deprecatedCodes = array_diff(range($minCode, $maxCode), $exceptionsCodes)) {
	echo 'Deprecated exception codes: ', implode(', ', $deprecatedCodes), '.', PHP_EOL, PHP_EOL;
}

foreach ($foundExceptions as $exception) {
	printException($exception);
}
