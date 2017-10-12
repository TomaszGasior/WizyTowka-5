<?php

/**
* WizyTówka 5
* Bootstrap for unit tests.
*/

// Load config with system constants.
include __DIR__ . '/../code/config.php';

// Init system without controller.
include WizyTowka\SYSTEM_DIR . '/init.php';

// Include workarounds.
include __DIR__ . '/workarounds.php';