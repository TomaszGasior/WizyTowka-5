<?php

/**
* WizyTówka 5
* System paths definitions.
*/
namespace WizyTowka;


/* You can edit this file.
   Do it only if you know, what are you doing. */

// "data" directory contains site configuration, plugins and files.
const DATA_DIR = __DIR__ . '/data';

// "files" directory contains files sent to website pages.
const FILES_DIR = DATA_DIR . '/files';

// "config" directory contains configuration file and database file.
const CONFIG_DIR = DATA_DIR . '/config';

// "system" directory contains CMS files. You must not edit contents of this folder.
const SYSTEM_DIR = __DIR__ . '/system';