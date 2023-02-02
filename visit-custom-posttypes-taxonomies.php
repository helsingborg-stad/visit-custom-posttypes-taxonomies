<?php

/*
 * Plugin Name: Visit Helsingborg Custom Post Types and Taxonomies
 */

 add_action('init', 'registerCustomPostTypesVisitHbg');

function registerCustomPostTypesVisitHbg()
{

    /**----------------------
    * PLATS
    * non-hierarchical
    *------------------------**/
    $labels = array(
        'name'                  => 'Platser',
        'singular_name'         => 'Plats',
        'menu_name'             => 'Platser',
        'name_admin_bar'        => 'Plats',
    );
    $rewrite = array(
        'slug'                  => 'plats',
        'with_front'            => false,
        'pages'                 => true,
        'feeds'                 => true,
    );
    $args = array(
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

    $allPlacePostTypes = ['place'];

    /**
     * TYP AV PLATS (Museum, Restaurang, Lekplats osv)
     * non-hierarchical
     */
    $labels = array(
        'name'                       => 'Platstyper',
        'singular_name'              => 'Typ av plats',
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
    register_taxonomy('type', $allPlacePostTypes, $args);

    /**
     * TYP AV AKTIVITET (Sevärdhet, Äta & Dricka, Shopping osv)
     * hierarchical
     */
    $labels = array(
        'name'                       => 'Aktiviteter',
        'singular_name'              => 'Aktivitet',
    );
    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => true,
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_quick_edit'         => true,
        'meta_box_cb'                => false,
        'show_in_nav_menus'          => false,
        'show_tagcloud'              => false,
        'rewrite'                    => false,
    );
    register_taxonomy('activity', $allPlacePostTypes, $args);

    /**
     * TYP AV KÖK (Vegetariskt, Italienskt, Pizza, Husmanskost osv)
     * non-hierarchical
     * Using terms rather than meta values to easily be able to add new ones that are shareable across posts.
     */
    $labels = array(
        'name'                       => 'Kök',
        'singular_name'              => 'Kök',
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
    register_taxonomy('cuisine', $allPlacePostTypes, $args);

    /**
     * ÖVRIGT
     * non-hierarchical
     * Using terms rather than meta values to easily be able to add new ones that are shareable across posts.
     */
    $labels = array(
        'name'                       => 'Övrigt',
        'singular_name'              => 'Övrigt',
        'menu_name'                  => 'Övrigt',
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
    register_taxonomy('other', $allPlacePostTypes, $args);

    /**----------------------
    *    GUIDER
    *------------------------**/
    $labels = array(
        'name'                  => 'Guider',
        'singular_name'         => 'Guide',
    );
    $rewrite = array(
        'slug'                  => 'guide',
        'with_front'            => false,
        'pages'                 => true,
        'feeds'                 => true,
    );
    $args = array(
        'label'                 => 'Guider',
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
