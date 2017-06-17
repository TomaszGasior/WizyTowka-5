<?php

/**
* WizyTówka 5
* This script prepares "code/data" directory for developing purposes.
*/
namespace WizyTowka;


const DATA_DIR   = __DIR__ . '/../code/data';
const CONFIG_DIR = DATA_DIR . '/config';
const SYSTEM_DIR = __DIR__ . '/../code/system';

include SYSTEM_DIR . '/init.php';


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

// Create "sessions.conf" config file.
ConfigurationFile::createNew(CONFIG_DIR . '/sessions.conf');

// Copy default "settings.conf" config file to data directory.
copy(
	SYSTEM_DIR . '/defaults/settings.conf',
	CONFIG_DIR . '/settings.conf'
);

// Set various settings.
$settings = Settings::get();
$settings->databaseType = 'sqlite';
$settings->websiteAddress = 'http://localhost';
$settings->websiteTitle = 'Przykładowa witryna';
$settings->websiteSiteHomepageId = 1;
$settings->systemVersion = VERSION;
$settings->systemShowErrors = true;

// Connect to SQLite database, create database file.
$databaseFile = CONFIG_DIR . '/database.db';
if (file_exists($databaseFile)) {
	unlink($databaseFile);
}
Database::connect('sqlite', $databaseFile);

// Generate database schema.
system('php '.__DIR__.'/GenerateDBSchema.php sqlite');
Database::executeSQL(file_get_contents('sqliteSchema.sql'));
unlink('sqliteSchema.sql');

// Example data: users.
foreach (range(1, 3) as $number) {
	$user = new User;
	$user->name = 'user_' . $number;
	$user->permissions = User::PERM_CREATING_PAGES | User::PERM_SUPER_USER;
	$user->setPassword($user->name);
	$user->save();
}

// Example data: pages.
foreach (range(1, 5) as $number) {
	$page = new Page;
	$page->slug = 'example_' . $number;
	$page->title = 'Przykładowa strona #' . $number;
	$page->isDraft = !($number % 2);
	$page->userId = 1;
	$page->save();
}