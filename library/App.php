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

        add_action('acf/save_post', [$this, 'setPostSingleShowFeaturedImage'], 1, 1);

        // Allow setting quick link colors
        add_filter('Municipio/Navigation/Item', [$this, 'quickLinkColors'], 10, 3);

        // Display breadcrumbs after the content on all posts
        add_filter('Municipio/Partials/Navigation/HelperNavBeforeContent', '__return_false');

        // Place Quicklinks below the content on all places
        add_filter('Municipio/QuickLinksPlacement', [$this, 'placeQuicklinksAfterContent'], 10, 2);

        // Only display current term and it's children in secondary query filter
        add_filter('Municipio/secondaryQuery/getTermsArgs', [$this, 'getTermsArgs'], 10, 2);

        //Handle search in the quicklinks menu
        add_filter('Municipio/Navigation/Item', [$this, 'quicklinksSearchMenuItem'], 10, 3);

        // Unlinked terms with term icons from custom taxonomy "other"
        add_filter('Municipio/Controller/SingularPlace/listing', [$this, 'appendListingItems'], 11, 2);
        // Order listing items
        add_filter('Municipio/Controller/SingularPlace/listing', [$this, 'orderListingItems'], 99, 1);

        // Print Bike Approved Accommodation info on places with the term
        add_filter('Municipio/Helper/Post/postObject', [$this, 'appendBikeApprovedAccommodationInfo'], 10, 1);

        add_filter('Municipio/Archive/showFilter', [$this, 'hideFiltersOnTerms'], 10, 2);

        // Cache invalidation hooks for performance optimization
        add_action('created_term', [$this, 'clearVisitCaches'], 10, 3);
        add_action('edited_term', [$this, 'clearVisitCaches'], 10, 3);
        add_action('delete_term', [$this, 'clearVisitCaches'], 10, 4);
        add_action('save_post', [$this, 'clearPostCaches'], 10, 3);
    }

    public function hideFiltersOnTerms($displayFilters, $args)
    {
        if (is_tax()) {
            $displayFilters = false;
        }
        return $displayFilters;
    }


    public function appendListingItems($listing, $fields)
    {
        if (empty($fields['other']) || !class_exists('\Municipio\Helper\Listing')) {
            return $listing;
        }

        // Use transient caching for listing items to avoid repeated processing
        $cache_key = 'listing_items_' . md5(serialize($fields['other']));
        $cached_items = get_transient($cache_key);
        
        if ($cached_items !== false) {
            $listing['other'] = $cached_items;
            return $listing;
        }

        $listing['other'] = [];
        foreach (\Municipio\Helper\Listing::getTermsWithIcon($fields['other']) as $term) {
            if (!is_array($term->icon)) {
                continue;
            }
            $listing['other'][$term->slug] = \Municipio\Helper\Listing::createListingItem(
                $term->name,
                '',
                $term->icon,
            );
        }

        // Cache for 1 hour
        set_transient($cache_key, $listing['other'], HOUR_IN_SECONDS);
        
        return $listing;
    }

    public function orderListingItems($listing)
    {

        $orderedListing = [];

        if (isset($listing['location'])) {
            $orderedListing['location'] = $listing['location'];
        }
        if (isset($listing['phone'])) {
            $orderedListing['phone'] = $listing['phone'];
        }
        if (isset($listing['website'])) {
            $orderedListing['website'] = $listing['website'];
        }
        if (isset($listing['other'])) {
            if (is_array($listing['other'])) {
                foreach ($listing['other'] as $key => $item) {
                    $orderedListing[$key] = $item;
                }
            } else {
                $orderedListing['other'] = $listing['other'];
            }
        }

        return $orderedListing;
    }

    /**
     * Adds search icon to main menu
     *
     * @param array     $data          Array containing the menu
     * @param string    $identifier    What menu being filtered
     *
     * @return array
     */
    public function quicklinksSearchMenuItem($data, $identifier, $pageId)
    {
        if ($data['href'] == '#search' && 'single' === $identifier) {
            $data = array_merge(
                $data,
                [
                "id" => "search-icon",
                "isSearch" => true,
                "classList" => ["c-nav__item--search"],
                "attributeList" => [
                    'aria-label' => __("Search", 'municipio'),
                    'data-open' => 'm-search-modal__trigger',
                ],
                ]
            );
            if (is_front_page() || is_search()) {
                $data["classList"] = ["u-display--none"];
            }
        }
        return $data;
    }
    public function placeQuicklinksAfterContent($placement, $postId)
    {
        if (get_post_type($postId) === 'place') {
            return "below_content";
        }
        return $placement;
    }
    public function quickLinkColors($item)
    {
        // Use object cache for ACF field values to reduce database queries
        $cache_key = 'menu_item_color_' . $item['id'];
        $color = wp_cache_get($cache_key, 'visit_plugin');
        
        if ($color === false) {
            $color = get_field('menu_item_color', $item['id']);
            // Cache for the duration of the request
            wp_cache_set($cache_key, $color, 'visit_plugin', 0);
        }
        
        $item['color'] = $color;
        return $item;
    }

    // Always set post_single_show_featured_image from "Display settings" to true
    public function setPostSingleShowFeaturedImage($postId)
    {
        $_POST['acf']['field_56c33e148efe3'] = 1;
    }
    /**
     * @param array $args The arguments passed to the get_terms() function.
     * @param array $data The data that is passed to the template.
     *
     * @return array An array of terms.
     */
    public function getTermsArgs(array $args = [], array $data = [])
    {
        $pageForTerms = $this->isPageForTerm();

        if (empty($pageForTerms) || !in_array($args['taxonomy'], ['activity', 'cuisine'])) {
            return $args;
        }

        // Create cache key for this specific request
        $cache_key = 'term_args_' . md5(serialize([$pageForTerms, $args['taxonomy']]));
        $cached_result = get_transient($cache_key);
        
        if ($cached_result !== false) {
            return $cached_result;
        }

        if ($args['taxonomy'] === 'cuisine') {
            // Batch fetch all terms at once instead of individual queries
            $terms = get_terms([
                'taxonomy' => 'activity',
                'include' => $pageForTerms,
                'hide_empty' => false,
                'fields' => 'all'
            ]);
            
            if (!empty($terms) && !is_wp_error($terms)) {
                foreach ($terms as $term) {
                    if ($this->isFoodRelated($term->slug)) {
                        // Cache the positive result for 1 hour
                        set_transient($cache_key, $args, HOUR_IN_SECONDS);
                        return $args;
                    }
                }
            }
            
            // Cache the negative result for 1 hour
            set_transient($cache_key, false, HOUR_IN_SECONDS);
            return false;
        }

        if (isset($args['taxonomy']) && $args['taxonomy'] == 'activity') {
            // Batch fetch all terms at once
            $terms = get_terms([
                'taxonomy' => 'activity',
                'include' => $pageForTerms,
                'hide_empty' => false,
                'fields' => 'all'
            ]);
            
            $termIdsToInclude = [];
            
            if (!empty($terms) && !is_wp_error($terms)) {
                foreach ($terms as $term) {
                    $termChildren = get_term_children($term->term_id, $term->taxonomy);
                    if (!empty($termChildren) && !is_wp_error($termChildren)) {
                        $termIdsToInclude = array_merge($termIdsToInclude, $termChildren);
                    }
                }
            }
            
            // No child terms found, no need to display the filter.
            if (empty($termIdsToInclude)) {
                set_transient($cache_key, false, HOUR_IN_SECONDS);
                return false;
            }
            
            // Child term found, include only those in the filter.
            $args['include'] = array_unique($termIdsToInclude);
            set_transient($cache_key, $args, HOUR_IN_SECONDS);
        }

        return $args;
    }

    /**
     * If the post has a value for the ACF field "is_page_for_term", return the value of that field.
     * Otherwise, return false.
     *
     * @param int postId The post ID of the page you want to check. If you don't pass this, it will use
     * the current page.
     *
     * @return An array of term objects.
     */
    public function isPageForTerm(int $postId = 0)
    {
        if (!$postId) {
            $postId = get_queried_object_id();
        }
        $terms = (array) get_field('is_page_for_term', $postId);
        if (empty($terms)) {
            return false;
        }
        return $terms;
    }
    /**
     * Checks if a given term name is related to a food activity.
     *
     * @param string termSlug The slug of the term you want to check.
     *
     * @return A boolean value.
     */
    public function isFoodRelated(string $termSlug = '')
    {
        return in_array(
            $termSlug,
            [
                'mat-dryck',
                'ata-dricka',
                'mat-och-dryck',
                'ata-och-dricka',
                'food',
                'food-beverage',
                'food-and-beverage',
            ]
        );
    }
    /**
     * Checks if a given term name is the term for the "Bike Approved Accomodation" certification.
     *
     * @param string termSlug The slug of the term you want to check.
     *
     * @return A boolean value.
     */
    public function isBikeApprovedAccommodation(string $termSlug = '')
    {
        return in_array(
            $termSlug,
            [
                'bike-approved-accommodation',
                'bike-approved-acommodation', // common misspelling of "accommodation"
                'bike-approved-accomodation', // common misspelling of "accommodation"
                'bike-approved',
                'bike-friendly-accommodation',
                'bike-friendly-acommodation', // common misspelling of "accommodation"
                'bike-friendly-places',
                'bike-friendly-place',
                'bike-friendly-location',
            ]
        );
    }

    /**
     * The function appends information about bike-approved accommodations to a post object if it has a
     * certain term.
     *
     * @param object $postObject
     *
     * @return $postObject
     */
    public function appendBikeApprovedAccommodationInfo($postObject)
    {
        // Early return if post_content_filtered doesn't exist
        if (!property_exists($postObject, 'post_content_filtered')) {
            return $postObject;
        }

        // Only process 'place' post type for performance
        if (get_post_type($postObject->ID) !== 'place') {
            return $postObject;
        }

        // Use transient caching for terms to avoid repeated database queries
        $cache_key = 'bike_approved_terms_' . $postObject->ID;
        $cached_result = get_transient($cache_key);
        
        if ($cached_result !== false) {
            if ($cached_result['has_bike_approved']) {
                $postObject->post_content_filtered .= $cached_result['content'];
            }
            return $postObject;
        }

        $terms = get_the_terms($postObject->ID, 'other');
        $result = ['has_bike_approved' => false, 'content' => ''];
        
        if (!empty($terms) && !is_wp_error($terms)) {
            foreach ($terms as $term) {
                if ($this->isBikeApprovedAccommodation($term->slug)) {
                    $description = get_field('description', $term) ?? term_description($term) ?? false;
                    $content = apply_filters('the_content', \render_blade_view(
                        'partials.bike-approved-accommodation',
                        [
                            'description' => str_replace(
                                ["[plats]","[place]"], // Replace with the name of the place being displayed.
                                $postObject->post_title,
                                $description
                            )
                        ]
                    ));
                    
                    $result = ['has_bike_approved' => true, 'content' => $content];
                    $postObject->post_content_filtered .= $content;
                    break;
                }
            }
        }

        // Cache for 1 hour to reduce database load
        set_transient($cache_key, $result, HOUR_IN_SECONDS);
        
        return $postObject;
    }

    /**
     * Clear relevant caches when terms are modified
     *
     * @param int $term_id The term ID
     * @param int $tt_id The term taxonomy ID
     * @param string $taxonomy The taxonomy name
     */
    public function clearVisitCaches($term_id, $tt_id = null, $taxonomy = null, $deleted_term = null)
    {
        // Clear term-related transients
        $this->clearTransientsByPattern('term_args_');
        $this->clearTransientsByPattern('listing_items_');
        
        // Clear object cache for menu colors
        wp_cache_flush_group('visit_plugin');
    }

    /**
     * Clear post-specific caches when posts are saved
     *
     * @param int $post_id The post ID
     * @param WP_Post $post The post object
     * @param bool $update Whether this is an update
     */
    public function clearPostCaches($post_id, $post, $update)
    {
        if (in_array($post->post_type, ['place', 'guide'])) {
            // Clear bike approved accommodation cache for this specific post
            delete_transient('bike_approved_terms_' . $post_id);
        }
    }

    /**
     * Clear transients by pattern (WordPress doesn't have a built-in way to do this efficiently)
     *
     * @param string $pattern The pattern to match
     */
    private function clearTransientsByPattern($pattern)
    {
        global $wpdb;
        
        // Get all transients that match the pattern
        $transients = $wpdb->get_col(
            $wpdb->prepare(
                "SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE %s",
                '_transient_' . $pattern . '%'
            )
        );
        
        foreach ($transients as $transient) {
            $key = str_replace('_transient_', '', $transient);
            delete_transient($key);
        }
    }
}
