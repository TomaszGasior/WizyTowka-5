<?php

/**
* WizyTówka 5
* This script prepares "code/data" directory for developing purposes.
*/
namespace WizyTowka;


include __DIR__ . '/../code/config.php';
include SYSTEM_DIR . '/init.php';


// This function will be moved to installer in the future.
function generateSchemaSQL($driver)
{
	$schemaTemplate = explode("\n", file_get_contents(SYSTEM_DIR . '/defaults/schema.sql'));
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


// Create structure of "data" directory.
$dataDirs = [
	'addons',
	'addons/plugins',
	'addons/types',
	'addons/themes',
	'config',
	'files',
];
foreach ($dataDirs as $directory) {
	$path = DATA_DIR . '/' . $directory;
	if (!file_exists($path)) {
		mkdir($path);
	}
}

// Create new "sessions.conf" config file.
ConfigurationFile::createNew(CONFIG_DIR . '/sessions.conf');

// Copy default "settings.conf" config file to data directory.
copy(
	SYSTEM_DIR . '/defaults/settings.conf',
	CONFIG_DIR . '/settings.conf'
);

// Set various settings.
$settings = Settings::get();
$settings->websiteAddress = 'http://wizytowka.localhost';
$settings->websiteTitle = 'Przykładowa witryna';
$settings->websiteHomepageId = 1;
$settings->systemVersion = VERSION;
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

// Clean up current database and create new.
$SQLiteDatabaseFile = CONFIG_DIR . '/database.db';
if (file_exists($SQLiteDatabaseFile)) {
	unlink($SQLiteDatabaseFile);
}
if ($settings->databaseType == 'mysql') {
	system('mysql -u ' . $settings->databaseUsername . ' -e "DROP DATABASE IF EXISTS ' . $settings->databaseName . '"');
	system('mysql -u ' . $settings->databaseUsername . ' -e "CREATE DATABASE ' . $settings->databaseName . '"');
}
if ($settings->databaseType == 'pgsql') {
	system('dropdb --if-exists ' . $settings->databaseName . ' -U ' . $settings->databaseUsername);
	system('createdb ' . $settings->databaseName . ' -U ' . $settings->databaseUsername);
}

// Connect to database.
Database::connect(
	$settings->databaseType,
	($settings->databaseType == 'sqlite') ? $SQLiteDatabaseFile : $settings->databaseName,
	$settings->databaseHost, $settings->databaseUsername, $settings->databasePassword
);

// Generate database schema.
Database::executeSQL(generateSchemaSQL($settings->databaseType));

// Example data: users.
foreach (range(1, 3) as $number) {
	$user = new User;
	$user->name = 'user_' . $number;
	$user->permissions = ($number == 1) ? 0b1111111111 : (($number == 2) ? User::PERM_MANAGE_PAGES : User::PERM_CREATE_PAGES);
	$user->setPassword($user->name);
	$user->save();
}

// Example data: pages.
$contentType = ContentType::getByName(Settings::get('adminPanelDefaultContentType'));
foreach (range(1, 5) as $number) {
	$page = new Page;
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