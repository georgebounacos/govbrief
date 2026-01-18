<?php
/**
 * GovBrief Child Theme - functions.php
 *
 * Modular architecture: all functionality split into inc/ files.
 */

// === Enqueue Parent + Custom Styles ===
function generatepress_child_enqueue_styles() {
    wp_enqueue_style('generatepress-parent-style', get_template_directory_uri() . '/style.css');
    wp_enqueue_style('govbrief-custom', get_stylesheet_directory_uri() . '/assets/css/govbrief-custom.css', array('generatepress-parent-style'), '1.0.0');
}
add_action('wp_enqueue_scripts', 'generatepress_child_enqueue_styles');


// === Load Modular Components ===
require_once get_stylesheet_directory() . '/inc/acf-fields.php';
require_once get_stylesheet_directory() . '/inc/cpt.php';
require_once get_stylesheet_directory() . '/inc/frontend.php';
require_once get_stylesheet_directory() . '/inc/shortcodes.php';
require_once get_stylesheet_directory() . '/inc/admin-tools.php';
