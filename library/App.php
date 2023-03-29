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
        if (class_exists('Visit\Acf')) {
            new Acf();
        }
        if (class_exists('Visit\BlockManager')) {
            new BlockManager();
        }

        load_plugin_textdomain('visit', false, dirname(plugin_basename(__DIR__)) . '/languages');

        add_action('admin_head', [$this, 'hideFieldGroup'], 1, 1);
        add_action('acf/save_post', [$this, 'setPostSingleShowFeaturedImage'], 1, 1);

        // Allow setting quick link colors
        add_filter('Municipio/Navigation/Item', [$this, 'quickLinkColors'], 10, 3);

        // Display breadcrumbs after the content on all posts
        add_filter('Municipio/Partials/Navigation/HelperNavBeforeContent', '__return_false');

        // Place Quicklinks below the content on all places
        add_filter('Municipio/Controller/Singular/displayQuicklinksAfterContent', [$this, 'placeQuicklinksAfterContent'], 10, 2);
    }

    public function placeQuicklinksAfterContent($displayAfterContent, $postId)
    {
        if (get_post_type($postId) == 'place') {
            return true;
        }
        return $displayAfterContent;
    }
    public function quickLinkColors($item)
    {
        $item['color'] = get_field('menu_item_color', $item['id']);
        return $item;
    }

    // Hide the Municipio field group "Display settings" from the post edit screen
    // Hide the field group for Quicklinks placement on post edit screen for places
    public function hideFieldGroup()
    {
        echo '<style type="text/css">
        #acf-group_56c33cf1470dc,
        .post-type-place #acf-group_64227d79a7f57 { display:none!important; }
        </style>';
    }
    // Always set post_single_show_featured_image from "Display settings" to true
    public function setPostSingleShowFeaturedImage($postId)
    {
        $_POST['acf']['field_56c33e148efe3'] = 1;
    }
}
