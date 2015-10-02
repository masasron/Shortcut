<?php

/*
  Plugin Name: Empty Plugin
  Description: An empty shortcut plugin.
  Version: 1.0.0
 */

// Make sure Shortcut is active
if (!class_exists('Shortcut')) {
    return;
}

/**
 * @ver array
 */
$files = array(
    'controllers/*.php',
    'app.php'
);

Shortcut::make(__FILE__)->requireAll($files);
