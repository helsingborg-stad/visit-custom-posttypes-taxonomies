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

        add_action('pre_get_posts', [$this, 'setupPageForTermSecondaryQuery']);

        load_plugin_textdomain('visit', false, dirname(plugin_basename(__FILE__)) . '/languages');

        add_filter('acf/fields/google_map/api', [$this, 'googleMapApiKey'], 10, 1);

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

    /**
     * Sets up a secondary query for the current page based on the is_page_for_term field.
     *
     * @param WP_Query $query The current WP_Query object.
     * @return void
     */
    public function setupPageForTermSecondaryQuery($query)
    {
        if (!$query->is_main_query()) {
            return;
        }

        $isPageForTerm = get_field('is_page_for_term', $query->queried_object_id);

        if (is_array($isPageForTerm) && !empty($isPageForTerm)) {
            $secondaryQueryArgs =
            [
            'post_type' => 'place',
            'tax_query' => [
                'relation' => 'OR',
            ],
            'posts_per_page' => get_option('posts_per_page'),
            ];
            foreach ($isPageForTerm as $termId) {
                $term = get_term($termId);
                if (!$term || is_wp_error($term)) {
                    continue;
                }
                $secondaryQueryArgs['tax_query'][] = [
                'taxonomy' => $term->taxonomy,
                'field' => 'term_id',
                'terms' => $term->term_id,
                ];
            }

            $secondaryQuery = new \WP_Query($secondaryQueryArgs);
            $query->set('secondary_query', $secondaryQuery);
        }
    }
}
