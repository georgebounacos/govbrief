<?php
/**
 * GovBrief Child Theme - functions.php
 *
 * Modular architecture: all functionality split into inc/ files.
 */

// === Preconnect Hints for Performance ===
function govbrief_preconnect_hints() {
    echo '<link rel="preconnect" href="https://s47619.pcdn.co">' . "\n";
    echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
}
add_action('wp_head', 'govbrief_preconnect_hints', 1);

// === Enqueue Parent + Custom Styles ===
function generatepress_child_enqueue_styles() {
    wp_enqueue_style('generatepress-parent-style', get_template_directory_uri() . '/style.css');
    wp_enqueue_style('govbrief-custom', get_stylesheet_directory_uri() . '/assets/css/govbrief-custom.css', array('generatepress-parent-style'), '1.0.0');
}
add_action('wp_enqueue_scripts', 'generatepress_child_enqueue_styles');

// === Defer Non-Critical Scripts ===
function govbrief_defer_scripts($tag, $handle) {
    $defer_scripts = array(
        'elementor-frontend',
        'elementor-pro-frontend',
        'jquery-migrate',
        'cv-js',
        'cvpro-js',
        'addtoany-jquery'
    );
    if (in_array($handle, $defer_scripts)) {
        return str_replace(' src=', ' defer src=', $tag);
    }
    return $tag;
}
add_filter('script_loader_tag', 'govbrief_defer_scripts', 10, 2);

// === Static Share Links (replaces AddToAny for better performance) ===
function govbrief_static_share_links($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    $url = urlencode(get_permalink($post_id));
    $title = urlencode(get_the_title($post_id));

    $links = array(
        'facebook' => array(
            'url' => "https://www.facebook.com/sharer/sharer.php?u={$url}",
            'label' => 'Facebook',
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>'
        ),
        'bluesky' => array(
            'url' => "https://bsky.app/intent/compose?text={$title}%20{$url}",
            'label' => 'Bluesky',
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12 10.8c-1.087-2.114-4.046-6.053-6.798-7.995C2.566.944 1.561 1.266.902 1.565.139 1.908 0 3.08 0 3.768c0 .69.378 5.65.624 6.479.815 2.736 3.713 3.66 6.383 3.364.136-.02.275-.039.415-.056-.138.022-.276.04-.415.054-3.645.422-6.897 1.466-3.68 5.18 3.556 4.108 5.012.766 5.673-.278.66 1.044 1.117 4.386 5.673.278 3.218-3.714-.036-4.758-3.68-5.18-.14-.014-.277-.032-.415-.054.14.017.279.036.415.056 2.67.296 5.568-.628 6.383-3.364.246-.828.624-5.79.624-6.478 0-.69-.139-1.861-.902-2.206-.659-.298-1.664-.62-4.3 1.24C16.046 4.748 13.087 8.687 12 10.8z"/></svg>'
        ),
        'reddit' => array(
            'url' => "https://www.reddit.com/submit?url={$url}&title={$title}",
            'label' => 'Reddit',
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0zm5.01 4.744c.688 0 1.25.561 1.25 1.249a1.25 1.25 0 0 1-2.498.056l-2.597-.547-.8 3.747c1.824.07 3.48.632 4.674 1.488.308-.309.73-.491 1.207-.491.968 0 1.754.786 1.754 1.754 0 .716-.435 1.333-1.01 1.614a3.111 3.111 0 0 1 .042.52c0 2.694-3.13 4.87-7.004 4.87-3.874 0-7.004-2.176-7.004-4.87 0-.183.015-.366.043-.534A1.748 1.748 0 0 1 4.028 12c0-.968.786-1.754 1.754-1.754.463 0 .898.196 1.207.49 1.207-.883 2.878-1.43 4.744-1.487l.885-4.182a.342.342 0 0 1 .14-.197.35.35 0 0 1 .238-.042l2.906.617a1.214 1.214 0 0 1 1.108-.701zM9.25 12C8.561 12 8 12.562 8 13.25c0 .687.561 1.248 1.25 1.248.687 0 1.248-.561 1.248-1.249 0-.688-.561-1.249-1.249-1.249zm5.5 0c-.687 0-1.248.561-1.248 1.25 0 .687.561 1.248 1.249 1.248.688 0 1.249-.561 1.249-1.249 0-.687-.562-1.249-1.25-1.249zm-5.466 3.99a.327.327 0 0 0-.231.094.33.33 0 0 0 0 .463c.842.842 2.484.913 2.961.913.477 0 2.105-.056 2.961-.913a.361.361 0 0 0 .029-.463.33.33 0 0 0-.464 0c-.547.533-1.684.73-2.512.73-.828 0-1.979-.196-2.512-.73a.326.326 0 0 0-.232-.095z"/></svg>'
        ),
        'threads' => array(
            'url' => "https://www.threads.net/intent/post?text={$title}%20{$url}",
            'label' => 'Threads',
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12.186 24h-.007c-3.581-.024-6.334-1.205-8.184-3.509C2.35 18.44 1.5 15.586 1.472 12.01v-.017c.03-3.579.879-6.43 2.525-8.482C5.845 1.205 8.6.024 12.18 0h.014c2.746.02 5.043.725 6.826 2.098 1.677 1.29 2.858 3.13 3.509 5.467l-2.04.569c-1.104-3.96-3.898-5.984-8.304-6.015-2.91.022-5.11.936-6.54 2.717C4.307 6.504 3.616 8.914 3.589 12c.027 3.086.718 5.496 2.057 7.164 1.43 1.783 3.631 2.698 6.54 2.717 2.623-.02 4.358-.631 5.8-2.045 1.647-1.613 1.618-3.593 1.09-4.798-.31-.71-.873-1.3-1.634-1.75-.192 1.352-.622 2.446-1.284 3.272-.886 1.102-2.14 1.704-3.73 1.79-1.202.065-2.361-.218-3.259-.801-1.063-.689-1.685-1.74-1.752-2.96-.065-1.182.408-2.256 1.33-3.022.88-.73 2.082-1.168 3.59-1.304 1.11-.1 2.135-.056 3.072.133-.018-.9-.244-1.58-.674-2.02-.498-.51-1.282-.77-2.332-.77h-.036c-.822.007-1.478.166-1.95.473l-1.074-1.727c.737-.457 1.71-.715 2.968-.753h.057c1.581 0 2.81.45 3.654 1.339.752.793 1.17 1.9 1.243 3.291.373.159.716.343 1.025.556 1.023.707 1.79 1.676 2.218 2.803.654 1.723.621 4.281-1.533 6.39-1.846 1.807-4.097 2.584-7.297 2.607zm-.186-6.217c1.406 0 2.385-.456 2.909-1.356.304-.521.474-1.188.508-1.989-1.827-.453-3.973-.327-4.969.27-.559.336-.837.782-.804 1.288.023.357.2.683.513.942.42.347 1.08.545 1.843.545z"/></svg>'
        ),
        'email' => array(
            'url' => "mailto:?subject={$title}&body={$url}",
            'label' => 'Email',
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M0 3v18h24V3H0zm21.518 2L12 12.713 2.482 5h19.036zM2 19V7.183l10 8.104 10-8.104V19H2z"/></svg>'
        )
    );

    $output = '<div class="govbrief-share-links" style="display:flex;gap:8px;flex-wrap:wrap;">';
    foreach ($links as $key => $link) {
        $output .= '<a href="' . esc_url($link['url']) . '" target="_blank" rel="noopener noreferrer" aria-label="Share on ' . esc_attr($link['label']) . '" style="display:inline-flex;align-items:center;justify-content:center;width:36px;height:36px;background:#f3f4f6;border-radius:50%;color:#374151;text-decoration:none;transition:background 0.2s;" onmouseover="this.style.background=\'#e5e7eb\'" onmouseout="this.style.background=\'#f3f4f6\'">' . $link['icon'] . '</a>';
    }
    $output .= '</div>';

    return $output;
}

// Shortcode for static share links
function govbrief_share_shortcode($atts) {
    $atts = shortcode_atts(array('id' => null), $atts);
    return govbrief_static_share_links($atts['id']);
}
add_shortcode('govbrief_share', 'govbrief_share_shortcode');

// === Substack CTA (lightweight replacement for embed) ===
function govbrief_substack_cta_shortcode($atts) {
    $atts = shortcode_atts(array(
        'text' => 'Get the daily brief in your inbox',
        'button' => 'Subscribe Free'
    ), $atts);

    return '<div class="govbrief-substack-cta" style="text-align:center;padding:24px;background:linear-gradient(135deg,#1a1a2e 0%,#16213e 100%);border-radius:12px;margin:20px 0;">
        <p style="color:#fff;font-size:1.1rem;margin:0 0 16px 0;">' . esc_html($atts['text']) . '</p>
        <a href="https://gbounacos.substack.com/subscribe" target="_blank" rel="noopener" style="display:inline-block;background:#ff6719;color:#fff;padding:12px 28px;border-radius:6px;text-decoration:none;font-weight:600;font-size:1rem;transition:background 0.2s;" onmouseover="this.style.background=\'#e55a14\'" onmouseout="this.style.background=\'#ff6719\'">' . esc_html($atts['button']) . '</a>
    </div>';
}
add_shortcode('govbrief_substack_cta', 'govbrief_substack_cta_shortcode');


// === Load Modular Components ===
require_once get_stylesheet_directory() . '/inc/acf-fields.php';
require_once get_stylesheet_directory() . '/inc/cpt.php';
require_once get_stylesheet_directory() . '/inc/frontend.php';
require_once get_stylesheet_directory() . '/inc/shortcodes.php';
require_once get_stylesheet_directory() . '/inc/admin-tools.php';
