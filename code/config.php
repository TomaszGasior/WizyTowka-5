<?php

/**
* WizyTówka 5
* System paths definitions.
*/
namespace WizyTowka;


/* You can edit this file.
   Do it only if you know, what you are doing. */

// It's a file system directory where URL hierarchy is starting.
const PUBLIC_DIR = __DIR__;

// "system" directory contains CMS files — you must not edit contents of this folder.
const SYSTEM_DIR = __DIR__ . '/system';
const SYSTEM_URL = 'system';

// "data" directory contains site configuration, plugins and files.
const DATA_DIR = __DIR__ . '/data';
const DATA_URL = 'data';

// "files" directory contains files sent to website pages.
const FILES_DIR = DATA_DIR . '/files';
const FILES_URL = DATA_URL . '/files';

// "config" directory contains secret files. It is a good idea to move it outisde public area.
const CONFIG_DIR = DATA_DIR . '/config';