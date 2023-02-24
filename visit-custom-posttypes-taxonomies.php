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

// TODO Split App into smaller subclasses
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
        add_action('init', [$this, 'setupPostTypes']);
        add_action('init', [$this, 'setupTaxonomies']);

        add_action('save_post_place', [$this,'normalizePlaceActivities'], 10, 3);

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
    public static function getPostTypes()
    {
        return [
        [
            'key'           => 'place',
            'hierarchical'  => false,
            'labels' => [
                'name'          => _x('Places', 'Post type pural', 'visit'),
                'singular_name' => _x('Place', 'Post type singular', 'visit'),
                'menu_name'     => _x('Places', 'Menu label', 'visit'),
            ],
            'menu_icon'     => 'dashicons-location',
            'rewrite'       =>  [
                'slug'                  => 'plats',
                'with_front'            => false,
                'pages'                 => true,
            ],
        ],
        [
            'key'           => 'guide',
            'has_archive'   => 'guider',
            'hierarchical'  => false,
            'labels' => [
                'name'          => _x('Guides', 'Post type pural', 'visit'),
                'singular_name' => _x('Guide', 'Post type singular', 'visit'),
                'menu_name'     => _x('Guides', 'Menu label', 'visit'),
            ],
            'menu_icon' => 'dashicons-thumbs-up',
            'rewrite'       =>  [
                'slug'                  => 'guider',
                'with_front'            => false,
                'pages'                 => true,
            ],
        ]
        ];
    }

    public static function getTaxonomies()
    {
        return
        [
        /**
         * TYP AV AKTIVITET (Sevärdhet, Äta & Dricka, Shopping osv)
         * (hierarchial)
         */
        [
            'labels'            => [
                'name'          => __('Activities', 'visit'),
                'singular_name' => __('Activity', 'visit'),
            ],
            'key'          => 'activity',
            'post_types'   => 'place',
            'hierarchical' => true,
            'show_ui'      => true,
        ],
        /**
         * ÖVRIGT
         * (non-hierarchial)
         */
        [
            'labels'            => [
                'name'          => __('Other', 'visit'),
                'singular_name' => _x('Other', 'Singular term name', 'visit'),
            ],
            'key' => 'other',
            'post_types'        => 'place',
            'hierarchical'      => false,
            'show_ui'      => true,
        ],
        /**
         * TYP AV KÖK (Vegetariskt, Italienskt, Pizza, Husmanskost osv)
         * non-hierarchical
         */
        [
            'labels'            => [
                'name'          => _x('Cuisine', 'Singular term name', 'visit'),
                'singular_name' => _x('Cuisine', 'Singular term name', 'visit'),
            ],
            'key'               => 'cuisine',
            'post_types'        => 'place',
            'hierarchical'      => false,
            'show_ui'      => true,
        ],
        ];
    }

    public function setupPostTypes()
    {
        foreach (self::getPostTypes() as $postType) {
            self::registerPostType($postType);
        }
    }
    public function setupTaxonomies()
    {
        foreach (self::getTaxonomies() as $taxonomy) {
            self::registerTaxonomy($taxonomy);
        }
    }

   /**
    * Registers a post type.
    *
    * @param array postTypeArgs An array of arguments for the post type.
    *
    * @return WP_Post_Type|WP_Error The registered post type object on success, WP_Error object on ailure.
    */
    protected static function registerPostType(array $postTypeArgs = [])
    {
        // Post type key must exist
        if (empty($postTypeArgs['key'])) {
            return false;
        }

        // Default argument values.
        // Will be overwritten if existing in $postTypeArgs.
        $args = [
        'supports'              => [ 'title', 'editor', 'thumbnail', 'revisions' ],
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 20,
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'has_archive'           => false,
        'show_in_rest'          => true,
        'capability_type'       => 'page',
        'labels' => [
            'archives'              => __('Item Archives', 'visit'),
            'attributes'            => __('Item Attributes', 'visit'),
            'parent_item_colon'     => __('Parent Item:', 'visit'),
            'all_items'             => __('All Items', 'visit'),
            'add_new_item'          => __('Add New', 'visit'),
            'add_new'               => __('Add New', 'visit'),
            'new_item'              => __('New Item', 'visit'),
            'edit_item'             => __('Edit Item', 'visit'),
            'update_item'           => __('Update Item', 'visit'),
            'view_item'             => __('View Item', 'visit'),
            'view_items'            => __('View Items', 'visit'),
            'search_items'          => __('Search Item', 'visit'),
            'not_found'             => __('Not found', 'visit'),
            'not_found_in_trash'    => __('Not found in Trash', 'visit'),
            'featured_image'        => __('Featured Image', 'visit'),
            'set_featured_image'    => __('Set featured image', 'visit'),
            'remove_featured_image' => __('Remove featured image', 'visit'),
            'use_featured_image'    => __('Use as featured image', 'visit'),
            'insert_into_item'      => __('Insert into item', 'visit'),
            'uploaded_to_this_item' => __('Uploaded to this item', 'visit'),
            'items_list'            => __('Items list', 'visit'),
            'items_list_navigation' => __('Items list navigation', 'visit'),
            'filter_items_list'     => __('Filter items list', 'visit'),
        ],
        ];

        foreach ($postTypeArgs as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $k => $v) {
                    $args[$key][$k] = $v;
                }
            } else {
                $args[$key] = $value;
            }
        }
        return register_post_type($postTypeArgs['key'], $args);
    }
    /**
     * Registers a taxonomy
     *
     * @param  array $taxonomyArgs An array of arguments for the taxonomy.
     *
     * @return WP_Taxonomy|WP_Error The registered taxonomy object on success, WP_Error object on failure.
     */
    protected static function registerTaxonomy(array $taxonomyArgs = [])
    {
        // Taxonomy key must exist
        if (empty($taxonomyArgs['key'])) {
            return false;
        }

        $postTypes = [];
        if ($types = self::getPostTypes()) {
            foreach ($types as $type) {
                $postTypes[] = $type['key'];
            }
        }

        $args                    =  [
        'hierarchical'       => false,
        'show_ui'            => false,
        'post_types'         => implode(',', $postTypes),
        'public'             => true,
        'show_ui'            => false,
        'show_admin_column'  => true,
        'show_in_quick_edit' => true,
        'meta_box_cb'        => false,
        'show_in_nav_menus'  => false,
        'show_tagcloud'      => false,
        'rewrite'            => false,
        ];
        foreach ($taxonomyArgs as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $k => $v) {
                    $args[$key][$k] = $v;
                }
            } else {
                $args[$key] = $value;
            }
        }
        return register_taxonomy($taxonomyArgs['key'], $args['post_types'], $args);
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
     * It checks if the post has any activities selected, and if so, it checks if any of the
     * activities' parents are not selected, and if so, it adds them to the list of selected activities
     *
     * @param postId The ID of the post being saved
     * @param post The post object.
     * @param update true if this is an existing post being updated, false if it's a new post
     */
    public function normalizePlaceActivities($postId, $post, $update)
    {
        if (isset($_POST['acf']['field_63dcbd00231bd'])) {
            $termIds = $_POST['acf']['field_63dcbd00231bd'];
            foreach ($termIds as $termId) {
                $term = get_term_by('term_id', $termId, 'activity');
                if (!is_wp_error($term)) {
                    $ancestors = get_ancestors($term->term_id, $term->taxonomy);
                    if (!is_wp_error($ancestors)) {
                        foreach ($ancestors as $ancestorId) {
                            if (!in_array($ancestorId, $_POST['acf']['field_63dcbd00231bd'], true)) {
                                array_push($_POST['acf']['field_63dcbd00231bd'], $ancestorId);
                            }
                        }
                    }
                }
            }
        }
    }
}

// Instantiate class
$visit = App::instance();
