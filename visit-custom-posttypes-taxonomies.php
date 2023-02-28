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

namespace Visit;

if (!defined('VISIT_PATH')) {
    define('VISIT_PATH', plugin_dir_path(__FILE__));
}
/**
 * Composer autoloader from plugin
 */
if (file_exists(VISIT_PATH . 'vendor/autoload.php')) {
    require_once VISIT_PATH . 'vendor/autoload.php';
}
/**
 * Autoload classes from plugin directory library/
 */
if (file_exists(VISIT_PATH . 'library/autoload.php')) {
    require_once VISIT_PATH . 'library/autoload.php';
}
class App
{
    /**
     * The unique instance of the plugin.
     *
     * @var Visit\App
     */
    private static $instance;

    /**
     * Gets an instance of our plugin.
     *
     * @return Visit\App
     */
    public static function instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }
    public function __construct()
    {
        new PostTypes();
        new Taxonomies();

        // Acf auto import and export ACF Fields
        add_action('plugins_loaded', function () {
            $acfExportManager = new \AcfExportManager\AcfExportManager();
            $acfExportManager->setTextdomain('visit');
            $acfExportManager->setExportFolder(VISIT_PATH . 'library/AcfFields/');
            $acfExportManager->autoExport([
                'visit-visitorinformation' => 'group_63f8b99f12d0f',
                'visit-location'           => 'group_63eb4a0aa476e',
                'visit-activity'           => 'group_63dcbd004f856',
                'visit-cuisine'            => 'group_63dbb0ca3dab5',
                'visit-other'              => 'group_63dd0967db81c',
            ]);
            $acfExportManager->import();
        });

        load_plugin_textdomain('visit', false, dirname(plugin_basename(__FILE__)) . '/languages');

        add_filter('acf/fields/google_map/api', [$this, 'googleMapApiKey'], 10, 1);
    }

   /**
    * If the constant GOOGLE_API_KEY is defined, then set the key property of the  array to the
    * value of the constant
    *
    * @param api The API key to use for the Google Maps API.
    *
    * @return The  array is being returned.
    */
    public function googleMapApiKey($api)
    {
        if (defined('GOOGLE_API_KEY')) {
            $api['key'] = GOOGLE_API_KEY;
        }

        return $api;
    }
}

// Instantiate class
$visit = App::instance();
