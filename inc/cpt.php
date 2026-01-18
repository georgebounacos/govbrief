<?php
/**
 * GovBrief Custom Post Types & Taxonomies
 *
 * CPT registrations and taxonomy attachments.
 */

if (!defined('ABSPATH')) exit;

// === Register Categories & Tags for Custom Post Type ===
function attach_taxonomies_to_cpt() {
    register_taxonomy_for_object_type('category', 'daily-headlines');
    register_taxonomy_for_object_type('post_tag', 'daily-headlines');
}
add_action('init', 'attach_taxonomies_to_cpt');

function include_cpt_in_category_archives($query) {
    if ($query->is_category() && $query->is_main_query() && !is_admin()) {
        $query->set('post_type', ['post', 'daily-headlines']);
    }
}
add_action('pre_get_posts', 'include_cpt_in_category_archives');


// === NJ Brief CPT Registration ===
function govbrief_register_nj_brief_cpt() {
    $args = array(
        'label' => 'NJ Brief',
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_icon' => 'dashicons-location-alt',
        'query_var' => true,
        'rewrite' => array('slug' => 'nj', 'with_front' => false),
        'capability_type' => 'post',
        'has_archive' => true,
        'hierarchical' => false,
        'menu_position' => 5,
        'supports' => array('title', 'editor', 'thumbnail'),
        'show_in_rest' => true
    );

    register_post_type('nj-brief', $args);
}
add_action('init', 'govbrief_register_nj_brief_cpt');


// === NJ Issue Number Tracking ===
function govbrief_get_nj_issue_number($date) {
    $issue_numbers = get_option('govbrief_nj_issue_numbers', array());

    if (isset($issue_numbers[$date])) {
        return $issue_numbers[$date];
    }

    return null;
}

function govbrief_set_nj_issue_number($date, $issue_number) {
    $issue_numbers = get_option('govbrief_nj_issue_numbers', array());
    $issue_numbers[$date] = $issue_number;
    update_option('govbrief_nj_issue_numbers', $issue_numbers);
}

function govbrief_get_next_nj_issue_number() {
    $issue_numbers = get_option('govbrief_nj_issue_numbers', array());

    if (empty($issue_numbers)) {
        return 1;
    }

    return max($issue_numbers) + 1;
}

add_action('wp_ajax_set_nj_issue_number', 'govbrief_ajax_set_nj_issue_number');
function govbrief_ajax_set_nj_issue_number() {
    check_ajax_referer('govbrief_nj_nonce', 'nonce');

    $date = sanitize_text_field($_POST['date']);
    $issue_number = intval($_POST['issue_number']);

    govbrief_set_nj_issue_number($date, $issue_number);

    wp_send_json_success(array(
        'issue_number' => $issue_number,
        'message' => 'Issue number saved'
    ));
}

add_action('wp_ajax_create_nj_brief_post', 'govbrief_create_nj_brief_post');
function govbrief_create_nj_brief_post() {
    check_ajax_referer('create_nj_brief', 'nonce');

    if (!current_user_can('edit_posts')) {
        wp_die('Unauthorized');
    }

    $selected_date = isset($_GET['date']) ? sanitize_text_field($_GET['date']) : date('Y-m-d');
    $brief_date_formatted = date('Ymd', strtotime($selected_date));
    $display_date = date('F j, Y', strtotime($selected_date));

    $existing = get_posts(array(
        'post_type' => 'nj-brief',
        'post_status' => 'any',
        'meta_key' => 'brief_date',
        'meta_value' => $brief_date_formatted,
        'posts_per_page' => 1
    ));

    if (!empty($existing)) {
        wp_redirect(admin_url('post.php?post=' . $existing[0]->ID . '&action=edit'));
        exit;
    }

    $post_title = $display_date;

    $post_id = wp_insert_post(array(
        'post_title' => $post_title,
        'post_type' => 'nj-brief',
        'post_status' => 'draft',
        'post_author' => get_current_user_id()
    ));

    if ($post_id) {
        update_field('brief_date', $brief_date_formatted, $post_id);

        $issue_number = govbrief_get_nj_issue_number($selected_date);
        if (!$issue_number) {
            $issue_number = govbrief_get_next_nj_issue_number();
            govbrief_set_nj_issue_number($selected_date, $issue_number);
        }

        wp_redirect(admin_url('post.php?post=' . $post_id . '&action=edit'));
        exit;
    }

    wp_die('Failed to create post');
}
