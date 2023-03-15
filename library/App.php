<?php

namespace Visit;

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

        if (class_exists('Visit\PostTypes')) {
            new PostTypes();
        }
        if (class_exists('Visit\Taxonomies')) {
            new Taxonomies();
        }
        if (class_exists('Visit\BlockManager')) {
            new BlockManager();
        }

        load_plugin_textdomain('visit', false, dirname(plugin_basename(__DIR__)) . '/languages');

        add_filter('acf/fields/google_map/api', [$this, 'googleMapApiKey'], 10, 1);

        // Acf auto import and export ACF Fields
        add_action('plugins_loaded', function () {
            $acfExportManager = new \AcfExportManager\AcfExportManager();
            $acfExportManager->setTextdomain('visit');
            $acfExportManager->setExportFolder(plugin_dir_path(__FILE__) . 'AcfFields/');
            $acfExportManager->autoExport([
            'visit-visitorinformation' => 'group_63f8b99f12d0f',
            'visit-location'           => 'group_63eb4a0aa476e',
            'visit-activity'           => 'group_63dcbd004f856',
            'visit-cuisine'            => 'group_63dbb0ca3dab5',
            'visit-other'              => 'group_63dd0967db81c',
            ]);
            $acfExportManager->import();
        });
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
