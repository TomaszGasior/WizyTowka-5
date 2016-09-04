<?php

/**
* WizyTówka 5
* Configuration file with CMS directories paths. You can edit this file.
*/
namespace WizyTowka;


// "data" directory contains site settings, plugins and files.
const DATA_DIR = __DIR__ . '/system';

// "files" directory contains files attached to pages on website.
const FILES_DIR = DATA_DIR . '/files';

// "system" directory contains CMS files. You should not edit contents of this folder.
const SYSTEM_DIR = __DIR__ . '/system';


// Initialize content management system.
include SYSTEM_DIR . '/init.php';