<?php

namespace Visit;

class Acf
{
    public function __construct()
    {
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
            'visit-weather'            => 'group_641c187122f99',
            'visit-quicklinks-colors'  => 'group_641d735c44589',
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
