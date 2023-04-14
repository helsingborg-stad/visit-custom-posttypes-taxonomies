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

        add_action('admin_head', [$this, 'hideFieldGroup'], 1, 1);
        add_action('acf/save_post', [$this, 'setPostSingleShowFeaturedImage'], 1, 1);

        // Allow setting quick link colors
        add_filter('Municipio/Navigation/Item', [$this, 'quickLinkColors'], 10, 3);

        // Display breadcrumbs after the content on all posts
        add_filter('Municipio/Partials/Navigation/HelperNavBeforeContent', '__return_false');

        // Place Quicklinks below the content on all places
        add_filter('Municipio/Controller/Singular/displayQuicklinksAfterContent', [$this, 'placeQuicklinksAfterContent'], 10, 2);

        // Only display current term and it's children in secondary query filter
        add_filter('Municipio/secondaryQuery/getTermsArgs', [$this, 'getTermsArgs'], 10, 2);

        //Handle search in the quicklinks menu
        add_filter('Municipio/Navigation/Item', [$this, 'quicklinksSearchMenuItem'], 10, 3);
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
    public function hideFieldGroup()
    {
        // Hide the Municipio field group "Display settings" from the post edit screen
        // Hide the Municipio field group "Page Header" from the post edit screen
        // Hide the field group for Quicklinks placement on post edit screen for places
        echo '<style type="text/css">
        #acf-group_56c33cf1470dc,
        .post-type-place #acf-group_64227d79a7f57,
        #acf-group_5fd1e418be4a8 
        { display:none!important; }
        </style>';
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

        if ($args['taxonomy'] === 'cuisine') {
            $pageForTerms = $this->isPageForTerm();
            foreach ($pageForTerms as $termId) {
                $term = get_term($termId);
                if (is_a($term, 'WP_Term') && 'activity' == $term->taxonomy) {
                    if ($this->isFoodRelated($term->slug)) {
                        // We've found at least one food-related activity on this page,
                        // so we can return the args for the cuisine filter and display it.
                        return $args;
                    }
                }
            }
            return false;
        }

        if (isset($args['taxonomy']) && $args['taxonomy'] == 'activity') {
            $termIdsToInclude = [];
            foreach ($pageForTerms as $termId) {
                $term = get_term($termId);
                if (is_a($term, 'WP_Term')) {
                    $termChildren = get_term_children($termId, $term->taxonomy);
                    if (!empty($termChildren) && !is_wp_error($termChildren)) {
                        $termIdsToInclude = array_merge($termIdsToInclude, $termChildren);
                    }
                }
            }
            // No child terms found, no need to display the filter.
            if (empty($termIdsToInclude)) {
                return false;
            }
            // Child term found, include only those in the filter.
            $args['include'] = $termIdsToInclude;
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
     * > This function checks if a given term name is related to a food activity.
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
}
