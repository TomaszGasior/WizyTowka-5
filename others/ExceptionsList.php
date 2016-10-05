<?php

/**
* WizyTówka 5
* This script reads source code, finds all CMS exceptions, deprecated and duplicated codes.
*/
namespace WizyTowka;


const SYSTEM_DIR = __DIR__ . '/../code/system';
include SYSTEM_DIR . '/init.php';


function findExceptions()
{
	$foundExceptions = [];
	$classesFiles = glob(SYSTEM_DIR . '/classes/*.php');

	foreach ($classesFiles as $filePath) {
		$fileLines = file($filePath, FILE_SKIP_EMPTY_LINES);

		foreach ($fileLines as $line) {
			if (strpos($line, 'throw new WTException') !== false) {
				$exception = [];
				$line = str_replace(['throw new WTException(', 'throw new WTException (', ');', ') ;'], null, trim($line));

				list($exception['code'], $exception['message']) = array_map('trim', array_map('strrev', explode(',', strrev($line), 2)));
				$exception['file'] = basename($filePath);

				$foundExceptions[] = $exception;
			}
		}
	}

	sort($foundExceptions);  // Exceptions will be sorted by code. "code" is first element in $exception array.
	return $foundExceptions;
}

function findDuplicatedCodes(array $foundExceptions)
{
	$exceptionsCodes = array_column($foundExceptions, 'code');
	$duplicatedCodes = [];

	foreach (array_unique($exceptionsCodes) as $code) {
		if (count(array_keys($exceptionsCodes, $code)) > 1) {
				$duplicatedCodes[] = $code;
		}
	}

	return $duplicatedCodes;
}

function findDeprecatedCodes(array $foundExceptions)
{
	$exceptionsCodes = array_column($foundExceptions, 'code');

	$minCode = current($exceptionsCodes);
	end($exceptionsCodes);
	$maxCode = current($exceptionsCodes);

	return array_values(array_diff(range($minCode, $maxCode), $exceptionsCodes));
}


if (isset($argv[1]) and $argv[1] == '--exceptions-duplicates') {  // For unit tests.
	echo json_encode(findDuplicatedCodes(findExceptions()));
	return;
}


echo 'WizyTówka ', VERSION, (VERSION_STABLE) ? '' : ' UNSTABLE', ' — system exceptions list', PHP_EOL, PHP_EOL;

$foundExceptions = findExceptions();

if ($duplicatedCodes = findDuplicatedCodes($foundExceptions)) {
	echo 'WARNING: duplicated exceptions codes: ', implode(', ', $duplicatedCodes), '.', PHP_EOL, PHP_EOL;
}
if ($deprecatedCodes = findDeprecatedCodes($foundExceptions)) {
	echo 'Deprecated exceptions codes: ', implode(', ', $deprecatedCodes), '.', PHP_EOL, PHP_EOL;
}

foreach ($foundExceptions as $exception) {
	echo '#', str_pad($exception['code'], 3, ' ', STR_PAD_LEFT),
		' — ', $exception['file'], PHP_EOL,
		'       ', $exception['message'], PHP_EOL;
}