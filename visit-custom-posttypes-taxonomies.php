<?php

/*
 * Plugin Name: Visit Helsingborg Custom Post Types and Taxonomies
 */

 add_action('init', 'registerCustomPostTypesVisitHbg');

function registerCustomPostTypesVisitHbg()
{

    /**----------------------
    *    PLATSER
    *------------------------**/
    $labels = array(
        'name'                  => _x('Places', 'Post Type General Name', 'municipio'),
        'singular_name'         => _x('Place', 'Post Type Singular Name', 'municipio'),
        'menu_name'             => __('Places', 'municipio'),
        'name_admin_bar'        => __('Place', 'municipio'),
        'archives'              => __('Item Archives', 'municipio'),
        'attributes'            => __('Item Attributes', 'municipio'),
        'parent_item_colon'     => __('Parent Item:', 'municipio'),
        'all_items'             => __('All Items', 'municipio'),
        'add_new_item'          => __('Add New Item', 'municipio'),
        'add_new'               => __('Add New', 'municipio'),
        'new_item'              => __('New Item', 'municipio'),
        'edit_item'             => __('Edit Item', 'municipio'),
        'update_item'           => __('Update Item', 'municipio'),
        'view_item'             => __('View Item', 'municipio'),
        'view_items'            => __('View Items', 'municipio'),
        'search_items'          => __('Search Item', 'municipio'),
        'not_found'             => __('Not found', 'municipio'),
        'not_found_in_trash'    => __('Not found in Trash', 'municipio'),
        'featured_image'        => __('Featured Image', 'municipio'),
        'set_featured_image'    => __('Set featured image', 'municipio'),
        'remove_featured_image' => __('Remove featured image', 'municipio'),
        'use_featured_image'    => __('Use as featured image', 'municipio'),
        'insert_into_item'      => __('Insert into item', 'municipio'),
        'uploaded_to_this_item' => __('Uploaded to this item', 'municipio'),
        'items_list'            => __('Items list', 'municipio'),
        'items_list_navigation' => __('Items list navigation', 'municipio'),
        'filter_items_list'     => __('Filter items list', 'municipio'),
    );
    $rewrite = array(
        'slug'                  => 'plats',
        'with_front'            => false,
        'pages'                 => true,
        'feeds'                 => true,
    );
    $args = array(
        'label'                 => __('Place', 'municipio'),
        'labels'                => $labels,
        'supports'              => array( 'title', 'editor', 'thumbnail', 'revisions' ),
        'taxonomies'            => array( 'type', 'other' ),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 15,
        'menu_icon'             => 'dashicons-location',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => false,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'rewrite'               => $rewrite,
        'capability_type'       => 'page',
    );
    register_post_type('place', $args);
    /**
     * PLATSTYPER
     * Not public.
     */
    $labels = array(
        'name'                       => _x('Typ av plats', 'Taxonomy General Name', 'text_domain'),
        'singular_name'              => _x('Typ av plats', 'Taxonomy Singular Name', 'text_domain'),
        'menu_name'                  => __('Typ av plats', 'text_domain'),
        'all_items'                  => __('Alla typer', 'text_domain'),
        'new_item_name'              => __('Ny typ', 'text_domain'),
        'add_new_item'               => __('Lägg till ny typ', 'text_domain'),
        'edit_item'                  => __('Edit Item', 'text_domain'),
        'update_item'                => __('Update Item', 'text_domain'),
        'view_item'                  => __('View Item', 'text_domain'),
        'separate_items_with_commas' => __('Separate items with commas', 'text_domain'),
        'add_or_remove_items'        => __('Add or remove items', 'text_domain'),
        'choose_from_most_used'      => __('Choose from the most used', 'text_domain'),
        'popular_items'              => __('Popular Items', 'text_domain'),
        'search_items'               => __('Search Items', 'text_domain'),
        'not_found'                  => __('Not Found', 'text_domain'),
        'no_terms'                   => __('No items', 'text_domain'),
        'items_list'                 => __('Items list', 'text_domain'),
        'items_list_navigation'      => __('Items list navigation', 'text_domain'),
    );
    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => false,
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_quick_edit'         => true,
        'meta_box_cb'                => false,
        'show_in_nav_menus'          => false,
        'show_tagcloud'              => false,
        'rewrite'                    => false,
    );
    register_taxonomy('type', array( 'place' ), $args);
    /**
     * CUISINES
     * Not public. Set display of meta box via ACF.
     * Using terms rather than meta values to easily be able to add new ones that are shareable across posts.
     */
    $labels = array(
        'name'                       => _x('Kök', 'Taxonomy General Name', 'text_domain'),
        'singular_name'              => _x('Kök', 'Taxonomy Singular Name', 'text_domain'),
        'menu_name'                  => __('Kök', 'text_domain'),
        'all_items'                  => __('All Items', 'text_domain'),
        'parent_item'                => __('Parent Item', 'text_domain'),
        'parent_item_colon'          => __('Parent Item:', 'text_domain'),
        'new_item_name'              => __('New Item Name', 'text_domain'),
        'add_new_item'               => __('Add New Item', 'text_domain'),
        'edit_item'                  => __('Edit Item', 'text_domain'),
        'update_item'                => __('Update Item', 'text_domain'),
        'view_item'                  => __('View Item', 'text_domain'),
        'separate_items_with_commas' => __('Separate items with commas', 'text_domain'),
        'add_or_remove_items'        => __('Add or remove items', 'text_domain'),
        'choose_from_most_used'      => __('Choose from the most used', 'text_domain'),
        'popular_items'              => __('Popular Items', 'text_domain'),
        'search_items'               => __('Search Items', 'text_domain'),
        'not_found'                  => __('Not Found', 'text_domain'),
        'no_terms'                   => __('No items', 'text_domain'),
        'items_list'                 => __('Items list', 'text_domain'),
        'items_list_navigation'      => __('Items list navigation', 'text_domain'),
    );
    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => false,
        'public'                     => true,
        'show_ui'                    => false,
        'show_admin_column'          => true,
        'show_in_quick_edit'         => true,
        'meta_box_cb'                => false,
        'show_in_nav_menus'          => false,
        'show_tagcloud'              => false,
        'rewrite'                    => false,
    );
    register_taxonomy('cuisine', array( 'place' ), $args);
    /**
     * OTHER
     * Not public. Set display of meta box via ACF.
     * Using terms rather than meta values to easily be able to add new ones that are shareable across posts.
     */
    $labels = array(
        'name'                       => _x('Övrigt', 'Taxonomy General Name', 'text_domain'),
        'singular_name'              => _x('Övrigt', 'Taxonomy Singular Name', 'text_domain'),
        'menu_name'                  => __('Övrigt', 'text_domain'),
        'all_items'                  => __('All Items', 'text_domain'),
        'parent_item'                => __('Parent Item', 'text_domain'),
        'parent_item_colon'          => __('Parent Item:', 'text_domain'),
        'new_item_name'              => __('New Item Name', 'text_domain'),
        'add_new_item'               => __('Add New Item', 'text_domain'),
        'edit_item'                  => __('Edit Item', 'text_domain'),
        'update_item'                => __('Update Item', 'text_domain'),
        'view_item'                  => __('View Item', 'text_domain'),
        'separate_items_with_commas' => __('Separate items with commas', 'text_domain'),
        'add_or_remove_items'        => __('Add or remove items', 'text_domain'),
        'choose_from_most_used'      => __('Choose from the most used', 'text_domain'),
        'popular_items'              => __('Popular Items', 'text_domain'),
        'search_items'               => __('Search Items', 'text_domain'),
        'not_found'                  => __('Not Found', 'text_domain'),
        'no_terms'                   => __('No items', 'text_domain'),
        'items_list'                 => __('Items list', 'text_domain'),
        'items_list_navigation'      => __('Items list navigation', 'text_domain'),
    );
    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => false,
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_quick_edit'         => true,
        'meta_box_cb'                => false,
        'show_in_nav_menus'          => false,
        'show_tagcloud'              => false,
        'rewrite'                    => false,
    );
    register_taxonomy('other', array( 'place' ), $args);
    /**----------------------
    *    GUIDER
    *------------------------**/
    $labels = array(
        'name'                  => _x('Guides', 'Post Type General Name', 'municipio'),
        'singular_name'         => _x('Guide', 'Post Type Singular Name', 'municipio'),
        'menu_name'             => __('Guides', 'municipio'),
        'name_admin_bar'        => __('Guide', 'municipio'),
        'archives'              => __('Item Archives', 'municipio'),
        'attributes'            => __('Item Attributes', 'municipio'),
        'parent_item_colon'     => __('Parent Item:', 'municipio'),
        'all_items'             => __('All Items', 'municipio'),
        'add_new_item'          => __('Add New Item', 'municipio'),
        'add_new'               => __('Add New', 'municipio'),
        'new_item'              => __('New Item', 'municipio'),
        'edit_item'             => __('Edit Item', 'municipio'),
        'update_item'           => __('Update Item', 'municipio'),
        'view_item'             => __('View Item', 'municipio'),
        'view_items'            => __('View Items', 'municipio'),
        'search_items'          => __('Search Item', 'municipio'),
        'not_found'             => __('Not found', 'municipio'),
        'not_found_in_trash'    => __('Not found in Trash', 'municipio'),
        'featured_image'        => __('Featured Image', 'municipio'),
        'set_featured_image'    => __('Set featured image', 'municipio'),
        'remove_featured_image' => __('Remove featured image', 'municipio'),
        'use_featured_image'    => __('Use as featured image', 'municipio'),
        'insert_into_item'      => __('Insert into item', 'municipio'),
        'uploaded_to_this_item' => __('Uploaded to this item', 'municipio'),
        'items_list'            => __('Items list', 'municipio'),
        'items_list_navigation' => __('Items list navigation', 'municipio'),
        'filter_items_list'     => __('Filter items list', 'municipio'),
    );
    $rewrite = array(
        'slug'                  => 'guide',
        'with_front'            => false,
        'pages'                 => true,
        'feeds'                 => true,
    );
    $args = array(
        'label'                 => __('Guides', 'municipio'),
        'labels'                => $labels,
        'supports'              => array( 'title', 'editor', 'thumbnail', 'revisions' ),
        'taxonomies'            => array( '' ),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 15,
        'menu_icon'             => 'dashicons-thumbs-up',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => false,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'rewrite'               => $rewrite,
        'capability_type'       => 'page',
    );
    register_post_type('guide', $args);
}
