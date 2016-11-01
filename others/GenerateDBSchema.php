<?php

/**
* WizyTówka 5
* Generates SQL file for database schema converted from "code/system/defaults/schema.sql".
  It will be removed in the future.
*/
namespace WizyTowka;


const SYSTEM_DIR = __DIR__ . '/../code/system';
include SYSTEM_DIR . '/init.php';


$schema = explode("\n", file_get_contents(SYSTEM_DIR.'/defaults/schema.sql'));
$filteredSchema = '';
$allDrivers = ['mysql', 'pgsql', 'sqlite'];

if (empty($_SERVER['argv'][1]) or !in_array($_SERVER['argv'][1], $allDrivers)) {
	die('You must specify proper DBMS. Possible values: '.implode(', ', $allDrivers).'.'.PHP_EOL);
}
else {
	$driver = $_SERVER['argv'][1];
}

foreach ($schema as $line) {
	if (preg_match('/-- wt_dbms: (?<not>! ){0,1}(?<dbms>.*)$/', $line, $matches)) {
		if ($matches['dbms'] != $driver xor $matches['not']) {
			continue;
		}
	}

	$line = preg_replace('/--.*/', '', $line);

	if (trim($line) == '') {
		continue;
	}

	$filteredSchema .= rtrim($line) . "\n";
}

file_put_contents($driver.'Schema.sql', $filteredSchema);