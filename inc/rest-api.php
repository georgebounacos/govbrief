<?php
/**
 * GovBrief Custom REST API Endpoints
 *
 * Provides public feed endpoints for external consumers.
 */

if (!defined('ABSPATH')) exit;

// === ICE Feed Endpoint ===
// Returns ICE-tagged daily-headlines posts for external feed consumers.
// Endpoint: /wp-json/govbrief/v1/ice-feed
add_action('rest_api_init', function () {
    register_rest_route('govbrief/v1', '/ice-feed', array(
        'methods'             => 'GET',
        'callback'            => 'govbrief_ice_feed_callback',
        'permission_callback' => '__return_true',
        'args'                => array(
            'per_page' => array(
                'default'           => 100,
                'sanitize_callback' => 'absint',
                'validate_callback' => function ($value) {
                    return $value >= 1 && $value <= 1000;
                },
            ),
        ),
    ));
});

function govbrief_ice_feed_callback($request) {
    $per_page = $request->get_param('per_page');

    // ICE tag ID on GovBrief
    $ice_tag_id = 148;

    $posts = get_posts(array(
        'post_type'      => 'daily-headlines',
        'post_status'    => 'publish',
        'posts_per_page' => $per_page,
        'tag__in'        => array($ice_tag_id),
        'orderby'        => 'date',
        'order'          => 'DESC',
    ));

    if (empty($posts)) {
        return new WP_REST_Response(array(), 200);
    }

    $items = array();
    foreach ($posts as $post) {
        // Get category names
        $categories = wp_get_post_categories($post->ID, array('fields' => 'names'));

        // ACF fields
        $source      = function_exists('get_field') ? get_field('headline_source', $post->ID) : '';
        $source_link = function_exists('get_field') ? get_field('headline_link', $post->ID) : '';
        $callout     = function_exists('get_field') ? get_field('story_callout', $post->ID) : '';

        $items[] = array(
            'id'          => $post->ID,
            'title'       => html_entity_decode(get_the_title($post), ENT_QUOTES, 'UTF-8'),
            'date'        => get_the_date('Y-m-d', $post),
            'link'        => get_permalink($post),
            'source_link' => $source_link ? $source_link : '',
            'source'      => $source ? $source : '',
            'callout'     => $callout ? $callout : '',
            'categories'  => $categories,
        );
    }

    $response = new WP_REST_Response($items, 200);
    // Cache for 1 hour on CDN/browser
    $response->header('Cache-Control', 'public, max-age=3600');

    return $response;
}
