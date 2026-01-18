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


// === Image Optimization for Core Web Vitals ===

// Add fetchpriority="high" to LCP images (featured images on single posts/homepage)
function govbrief_add_fetchpriority_to_lcp($html, $post_id, $post_thumbnail_id) {
    if (empty($html)) {
        return $html;
    }

    // Add fetchpriority for above-the-fold images
    if (is_singular() || is_front_page() || is_home()) {
        // Only add if not already present
        if (strpos($html, 'fetchpriority') === false) {
            $html = str_replace('<img ', '<img fetchpriority="high" ', $html);
        }
    }

    return $html;
}
add_filter('post_thumbnail_html', 'govbrief_add_fetchpriority_to_lcp', 5, 3);

// Add loading="lazy" to content images (below the fold)
function govbrief_add_lazy_loading_to_content_images($content) {
    if (empty($content)) {
        return $content;
    }

    // Add loading="lazy" to images that don't have it
    $content = preg_replace_callback(
        '/<img([^>]+)>/i',
        function($matches) {
            $img_tag = $matches[0];
            // Skip if already has loading attribute or fetchpriority (LCP images)
            if (strpos($img_tag, 'loading=') !== false || strpos($img_tag, 'fetchpriority') !== false) {
                return $img_tag;
            }
            return str_replace('<img ', '<img loading="lazy" ', $img_tag);
        },
        $content
    );

    return $content;
}
add_filter('the_content', 'govbrief_add_lazy_loading_to_content_images', 15);

// === Share Buttons on Single Posts ===
function govbrief_append_share_buttons($content) {
    // Only on single posts (not pages, not custom post types, not in admin)
    if (!is_singular('post') || is_admin()) {
        return $content;
    }

    // Get share buttons HTML
    $share_html = '<div class="govbrief-share-wrapper" style="margin-top:32px;padding-top:24px;border-top:1px solid #e5e7eb;">';
    $share_html .= '<p style="margin:0 0 12px 0;font-size:0.9rem;color:#6b7280;font-weight:500;">Share this brief:</p>';
    $share_html .= govbrief_static_share_links(get_the_ID());
    $share_html .= '</div>';

    return $content . $share_html;
}
add_filter('the_content', 'govbrief_append_share_buttons', 20);


// Ensure images have width and height to prevent CLS
function govbrief_add_image_dimensions($html, $post_id, $post_thumbnail_id) {
    if (empty($html) || !$post_thumbnail_id) {
        return $html;
    }

    // Check if dimensions already exist
    if (preg_match('/width=["\']\d+["\']/', $html) && preg_match('/height=["\']\d+["\']/', $html)) {
        return $html;
    }

    // Get image dimensions
    $image_data = wp_get_attachment_image_src($post_thumbnail_id, 'full');
    if ($image_data && !empty($image_data[1]) && !empty($image_data[2])) {
        $width = $image_data[1];
        $height = $image_data[2];

        // Add dimensions if missing
        if (strpos($html, 'width=') === false) {
            $html = str_replace('<img ', '<img width="' . esc_attr($width) . '" ', $html);
        }
        if (strpos($html, 'height=') === false) {
            $html = str_replace('<img ', '<img height="' . esc_attr($height) . '" ', $html);
        }
    }

    return $html;
}
add_filter('post_thumbnail_html', 'govbrief_add_image_dimensions', 8, 3);
