<?php

/*
 * Plugin Name: Visit Helsingborg: Custom Post Types, Taxonomies and ACF Fields
 * Plugin URI: -
 * Description:
 * Version: 0.1
 * Author: Anna Johansson
 * Author URI: -
 * Text domain: visit
 */

/**
 * Composer autoloader from plugin
 */

if (file_exists(plugin_dir_path(__FILE__) . 'vendor/autoload.php')) {
    require_once plugin_dir_path(__FILE__) . 'vendor/autoload.php';
}

/**
 * Autoload files in library folder
 */
foreach (glob(plugin_dir_path(__FILE__) . 'library/*.php') as $file) {
    require_once $file;
}

/**
 * Instantiate main plugin class
 */
Visit\App::instance();
