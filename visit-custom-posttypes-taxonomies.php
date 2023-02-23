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

add_action('init', function () {
    load_plugin_textdomain('visit', false, VISIT_PATH . 'languages');
});
/**
 * Composer autoloader from plugin
 */
if (file_exists(VISIT_PATH . 'vendor/autoload.php')) {
    require_once VISIT_PATH . 'vendor/autoload.php';
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

        add_action('init', [$this, 'setupPostTypes']);
        add_action('init', [$this, 'setupTaxonomies']);

        load_plugin_textdomain('visit', false, dirname(plugin_basename(__FILE__)) . '/languages');
        // Acf auto import and export ACF Fields
        add_action('plugins_loaded', function () {
            $acfExportManager = new \AcfExportManager\AcfExportManager();
            $acfExportManager->setTextdomain('visit');
            $acfExportManager->setExportFolder(VISIT_PATH . 'library/AcfFields/');
            $acfExportManager->autoExport([
                'visit-activity' => 'group_63dcbd004f856',
                'visit-cuisine' => 'group_63dbb0ca3dab5',
                'visit-other' => 'group_63dd0967db81c',
            ]);
            $acfExportManager->import();
        });
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
                    'slug'                  => 'platser',
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
    * Register ACF field groups.
    *
    * @return void
    */
    public static function setupAcfFieldGroups()
    {

        if (function_exists('acf_add_local_field_group')) :

            /**----------------------
             * ACTIVITIES
             *------------------------**/

                acf_add_local_field_group(array(
                    'key' => 'group_63dcbd004f856',
                    'title' => __('Activities', 'visit'),
                    'fields' => array(
                        array(
                            'key' => 'field_63dcbd00231bd',
                            'label' => __('Activities', 'visit'),
                            'name' => 'activities',
                            'aria-label' => '',
                            'type' => 'taxonomy',
                            'instructions' => '',
                            'required' => 0,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '',
                                'class' => '',
                                'id' => '',
                            ),
                            'taxonomy' => 'activity',
                            'add_term' => 1,
                            'save_terms' => 1,
                            'load_terms' => 1,
                            'return_format' => 'id',
                            'field_type' => 'multi_select',
                            'allow_null' => 0,
                            'acfe_bidirectional' => array(
                                'acfe_bidirectional_enabled' => '0',
                            ),
                            'multiple' => 0,
                        ),
                    ),
                    'location' => array(
                        array(
                            array(
                                'param' => 'post_type',
                                'operator' => '==',
                                'value' => 'place',
                            ),
                        ),
                    ),
                    'menu_order' => 0,
                    'position' => 'normal',
                    'style' => 'default',
                    'label_placement' => 'left',
                    'instruction_placement' => 'label',
                    'hide_on_screen' => '',
                    'active' => true,
                    'description' => '',
                    'show_in_rest' => 1,
                    'acfe_display_title' => '',
                    'acfe_autosync' => '',
                    'acfe_form' => 0,
                    'acfe_meta' => '',
                    'acfe_note' => '',
                ));

            /**----------------------
             * CUISINE
             *------------------------**/
            acf_add_local_field_group(array(
            'key' => 'group_63dbb0ca3dab5',
            'title' => __('Cuisine', 'visit'),
            'fields' => array(
                array(
                    'key' => 'field_63dbb0ca18ed4',
                    'label' => __('Cuisine', 'visit'),
                    'name' => 'cuisine',
                    'aria-label' => '',
                    'type' => 'taxonomy',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'taxonomy' => 'cuisine',
                    'add_term' => 1,
                    'save_terms' => 1,
                    'load_terms' => 1,
                    'return_format' => 'id',
                    'field_type' => 'multi_select',
                    'allow_null' => 1,
                    'acfe_bidirectional' => array(
                        'acfe_bidirectional_enabled' => '0',
                    ),
                    'multiple' => 0,
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_taxonomy',
                        'operator' => '==',
                        'value' => 'activity:ata-dricka',
                    ),
                ),
            ),
            'menu_order' => 0,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'left',
            'instruction_placement' => 'label',
            'hide_on_screen' => '',
            'active' => true,
            'description' => '',
            'show_in_rest' => 1,
            'acfe_display_title' => '',
            'acfe_autosync' => '',
            'acfe_form' => 0,
            'acfe_meta' => '',
            'acfe_note' => __('Display on places categorised under "Food & Drink"', 'visit'),
            ));
        endif;
    }
}

// Instantiate class
$visit = App::instance();
