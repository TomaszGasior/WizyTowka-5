<?php

/**
* WizyTówka 5
* Index file. Configuration of system directories paths. You can edit this file.
*/
namespace WizyTowka;


// "data" directory contains site settings, plugins and files.
const DATA_DIR = __DIR__ . '/data';

// "files" directory contains files attached to pages on website.
const FILES_DIR = DATA_DIR . '/files';

// "config" directory contains configuration file and optional database file.
const CONFIG_DIR = DATA_DIR . '/config';

// "system" directory contains CMS files. You should not edit contents of this folder.
const SYSTEM_DIR = __DIR__ . '/system';



const INIT = 1;
require SYSTEM_DIR . '/init.php';