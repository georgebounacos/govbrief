<?php
/**
 * GovBrief Frontend Filters & Scripts
 *
 * Frontend filters, scripts, and display modifications.
 */

if (!defined('ABSPATH')) exit;

// === Add External Links Script (in Footer) ===
function add_external_links_script() {
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('a[href]').forEach(function(link) {
                if (link.hostname !== window.location.hostname) {
                    link.setAttribute('target', '_blank');
                    link.setAttribute('rel', 'noopener noreferrer');
                }
            });
        });
    </script>";
}
add_action('wp_footer', 'add_external_links_script');


// === Modify Content Views Button to Display "View Details" ===
function modify_content_views_button_text($html) {
    static $processing = false;
    if ($processing) return $html;

    $processing = true;

    $pattern = '/<div\s+class=["\']pt-cv-ctf-value["\']>(.*?)<\/div>/is';

    $result = preg_replace_callback($pattern, function($matches) {
        $content = trim($matches[1]);

        if (preg_match('/https?:\/\/\S+/i', $content)) {
            if (strpos($content, '<a') === false) {
                preg_match('/https?:\/\/[^\s<>"]+/i', $content, $url_matches);
                $url = trim($url_matches[0]);
                return '<div class="pt-cv-ctf-value"><a href="' . esc_url($url) . '">View Details</a></div>';
            } else {
                $link_pattern = '/<a\s+([^>]*)>(.*?)<\/a>/is';
                $replacement = '<a $1>View Details</a>';
                $content_with_button = preg_replace($link_pattern, $replacement, $content);
                return '<div class="pt-cv-ctf-value">' . $content_with_button . '</div>';
            }
        }

        return $matches[0];
    }, $html);

    $processing = false;
    return $result;
}
add_filter('pt_cv_display_postmeta', 'modify_content_views_button_text', 99);
add_filter('the_content', 'modify_content_views_button_text', 99);


// === Force Elementor Google Fonts to HTTPS ===
add_filter('elementor/frontend/print_google_fonts_url', function($url) {
    return str_replace('http://','https://',$url);
});


// === Caption on Featured Image ===
add_filter('post_thumbnail_html', 'custom_add_post_thumbnail_caption', 10, 3);
function custom_add_post_thumbnail_caption($html, $post_id, $post_thumbnail_id) {
    if (empty($html)) {
        return $html;
    }

    $caption = wp_get_attachment_caption($post_thumbnail_id);

    if ($caption && strpos($html, 'wp-caption-text') === false) {
        return '<div class="wp-caption thumb-caption">'
               . $html
               . '<p class="wp-caption-text thumb-caption-text">' . esc_html($caption) . '</p></div>';
    }

    return $html;
}
