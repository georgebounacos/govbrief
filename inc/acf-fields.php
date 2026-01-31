<?php
/**
 * GovBrief ACF Field Groups & Filters
 *
 * All ACF field group registrations and ACF-related filters.
 */

if (!defined('ABSPATH')) exit;

// ========== ACF: Quote fields on Post editor ==========
add_action('acf/init', function () {
    if (!function_exists('acf_add_local_field_group')) return;

    acf_add_local_field_group([
        'key' => 'group_gbt_quote_block',
        'title' => 'GovBrief Quote',
        'fields' => [
            [
                'key' => 'field_gbt_quote_text',
                'label' => 'Quote Text',
                'name' => 'gbt_quote_text',
                'type' => 'textarea',
                'instructions' => 'Paste the quote exactly as you want it to appear.',
                'required' => 0,
                'rows' => 5,
                'new_lines' => 'br',
            ],
            [
                'key' => 'field_gbt_quote_cite',
                'label' => 'Quote Citation',
                'name' => 'gbt_quote_citation',
                'type' => 'text',
                'instructions' => 'Attribution, source, date.',
                'required' => 0,
                'default_value' => '',
                'placeholder' => 'Name, Source, Date',
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'post',
                ],
            ],
        ],
        'position' => 'normal',
        'style' => 'default',
        'active' => true,
    ]);
});


// ========== ACF: Yesterday's Most Read field group ==========
add_action('acf/init', function () {
    if (!function_exists('acf_add_local_field_group')) return;

    acf_add_local_field_group(array(
        'key' => 'group_gbt_most_read',
        'title' => "Yesterday's Most Read",
        'fields' => array(
            array(
                'key' => 'field_gbt_mr_blurb',
                'label' => 'Recap Blurb',
                'name' => 'gbt_mr_blurb',
                'type' => 'textarea',
                'instructions' => 'One sentence recap for why this mattered.',
                'rows' => 3,
                'new_lines' => 'br',
                'required' => 0,
            ),
            array(
                'key' => 'field_gbt_mr_url',
                'label' => 'Link URL',
                'name' => 'gbt_mr_url',
                'type' => 'url',
                'instructions' => 'Full link to yesterday\'s most read piece.',
                'required' => 0,
            ),
            array(
                'key' => 'field_gbt_mr_button',
                'label' => 'Button Label',
                'name' => 'gbt_mr_button',
                'type' => 'text',
                'default_value' => 'Catch Up',
                'required' => 0,
            ),
        ),
        'location' => array(
            array(
                array('param' => 'post_type', 'operator' => '==', 'value' => 'post'),
            ),
        ),
        'position' => 'normal',
        'style' => 'default',
        'active' => true,
    ));
});


// === Theme Options Setup ===
if( function_exists('acf_add_options_page') ) {
    acf_add_options_page(array(
        'page_title'  => 'Theme Settings',
        'menu_title'  => 'Theme Settings',
        'menu_slug'   => 'theme-settings',
        'capability'  => 'edit_posts',
        'redirect'    => false
    ));
}


// === ACF Field: Severity Level on daily-headlines ===
add_action('acf/init', function() {
    if (!function_exists('acf_add_local_field_group')) return;

    acf_add_local_field_group([
        'key' => 'group_govbrief_severity',
        'title' => 'Story Severity',
        'fields' => [
            [
                'key' => 'field_govbrief_severity_level',
                'label' => 'Severity Level',
                'name' => 'severity_level',
                'type' => 'radio',
                'instructions' => 'Level 1: Routine (normal in most administrations). Level 2: New Normal (significant but normalizing). Level 3: Defining Moment (will characterize this administration).',
                'required' => 0,
                'choices' => [
                    1 => 'Level 1: Routine',
                    2 => 'Level 2: New Normal',
                    3 => 'Level 3: Defining Moment',
                ],
                'default_value' => 1,
                'layout' => 'horizontal',
                'return_format' => 'value',
            ],
            [
                'key' => 'field_govbrief_defining_moment_summary',
                'label' => 'Defining Moment Summary',
                'name' => 'defining_moment_summary',
                'type' => 'text',
                'instructions' => 'Short 3-5 word summary for the intensity box (e.g., "FBI seizes Georgia ballots", "Asylum family detained").',
                'required' => 0,
                'maxlength' => 60,
                'placeholder' => 'e.g., FBI seizes Georgia ballots',
                'conditional_logic' => [
                    [
                        [
                            'field' => 'field_govbrief_severity_level',
                            'operator' => '==',
                            'value' => '3',
                        ],
                    ],
                ],
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'daily-headlines',
                ],
            ],
        ],
        'position' => 'normal',
        'menu_order' => 5,
        'style' => 'default',
        'label_placement' => 'top',
        'active' => true,
    ]);
});


// === Kill ACF Log Spam ===
// Mute the "acf' domain was triggered too early" notice
add_filter('doing_it_wrong_trigger_error', function($trigger, $function, $message, $version){
    if (strpos($message, "acf' domain was triggered too early") !== false) {
        return false;
    }
    return $trigger;
}, 10, 4);

// Always make ACF text-based fields return a string
add_filter('acf/format_value/type=text', function($value){
    return is_string($value) ? $value : '';
}, 99);

add_filter('acf/format_value/type=textarea', function($value){
    return is_string($value) ? $value : '';
}, 99);

add_filter('acf/format_value/type=wysiwyg', function($value){
    return is_string($value) ? $value : '';
}, 99);
