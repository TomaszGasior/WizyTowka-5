#!/usr/bin/php
<?php

/**
* WizyTówka 5
* This script prepares "code/data" directory for developing purposes.
*/
namespace WizyTowka\Tools;
use WizyTowka as __;


include __DIR__ . '/../code/config.php';
include __\SYSTEM_DIR . '/init.php';


// This function will be moved to installer in the future.
function generateSchemaSQL($driver)
{
	$schemaTemplate = explode("\n", file_get_contents(__\SYSTEM_DIR . '/defaults/schema.sql'));
	$schema = '';

	foreach ($schemaTemplate as $line) {
		if (preg_match('/-- wt_dbms: (?<not>! ){0,1}(?<dbms>[a-z]*)/', $line, $matches)) {
			if ($matches['dbms'] != $driver xor $matches['not']) {
				continue;
			}
		}

		$line = preg_replace('/--.*/', '', $line);

		if (trim($line) == '') {
			continue;
		}

		$schema .= rtrim($line) . "\n";
	}

	return $schema;
}


// Remove current "data" directory.
$oldDataDir = __\DATA_DIR . '_' . date('Y-m-d') . '_' . time();
rename(__\DATA_DIR, $oldDataDir);
system('gio trash ' . $oldDataDir);

// Create structure of "data" directory.
mkdir(__\DATA_DIR);
$dataDirs = [
	'addons',
	'addons/plugins',
	'addons/types',
	'addons/themes',
	'config',
	'files',
];
foreach ($dataDirs as $directory) {
	mkdir(__\DATA_DIR . '/' . $directory);
}

// Create new "sessions.conf" config file.
__\ConfigurationFile::createNew(__\CONFIG_DIR . '/sessions.conf');

// Copy default "settings.conf" config file to data directory.
copy(
	__\SYSTEM_DIR . '/defaults/settings.conf',
	__\CONFIG_DIR . '/settings.conf'
);
$settings = new __\ConfigurationFile(__\CONFIG_DIR . '/settings.conf');

// Set various settings.
$settings->websiteAddress = 'http://wizytowka.localhost';
$settings->websiteTitle = 'Przykładowa witryna';
$settings->websiteHomepageId = 1;
$settings->systemVersion = __\VERSION;
$settings->systemShowErrors = true;

// Set database settings.
if (empty($argv[1]) or $argv[1] == 'sqlite') {
	$settings->databaseType = 'sqlite';
}
elseif ($argv[1] == 'mysql' or $argv[1] == 'pgsql') {
	$settings->databaseType = $argv[1];
	$settings->databaseName = 'wizytowka';
	$settings->databaseHost = 'localhost';
	$settings->databaseUsername = 'wizytowka';
	$settings->databasePassword = '';
}
else {
	die('Wrong database type!' . PHP_EOL);
}

// Clean up current database (if exists) and create new.
if ($settings->databaseType == 'mysql') {
	system('mysql -u ' . $settings->databaseUsername . ' -e "DROP DATABASE IF EXISTS ' . $settings->databaseName . '"');
	system('mysql -u ' . $settings->databaseUsername . ' -e "CREATE DATABASE ' . $settings->databaseName . '"');
}
if ($settings->databaseType == 'pgsql') {
	system('dropdb --if-exists ' . $settings->databaseName . ' -U ' . $settings->databaseUsername);
	system('createdb ' . $settings->databaseName . ' -U ' . $settings->databaseUsername);
}

// Connect to database.
$databasePDO = new __\_Private\DatabasePDO(
	$settings->databaseType,
	($settings->databaseType == 'sqlite' ? __\CONFIG_DIR . '/database.db' : $settings->databaseName),
	$settings->databaseHost, $settings->databaseUsername, $settings->databasePassword
);
__\WT()->overwrite('database', $databasePDO);

// Generate database schema.
$databasePDO->exec(generateSchemaSQL($settings->databaseType));

// Example data: users.
foreach (range(1, 3) as $number) {
	$user = new __\User;
	$user->name = 'user_' . $number;
	$user->permissions = ($number == 1) ? 0b1111111111 : (($number == 2) ? __\User::PERM_MANAGE_PAGES : __\User::PERM_CREATE_PAGES);
	$user->setPassword($user->name);
	$user->save();
}

// Example data: pages.
$contentType = __\ContentType::getByName($settings->adminPanelDefaultContentType);
foreach (range(1, 5) as $number) {
	$page = new __\Page;
	$page->slug = 'example_' . $number;
	$page->title = 'Przykładowa strona #' . $number;
	$page->isDraft = !($number % 2);
	$page->noIndex = false;
	$page->contentType = $contentType->getName();
	$page->settings = (object)$contentType->settings;
	$page->contents = (object)$contentType->contents;
	$page->userId = 1;
	$page->save();
}