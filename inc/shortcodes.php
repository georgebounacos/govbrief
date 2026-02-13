<?php
/**
 * GovBrief Shortcodes
 *
 * All shortcode definitions for the GovBrief theme.
 */

if (!defined('ABSPATH')) exit;

// === Shortcode: Display Daily Headlines (Frontend) ===
function display_daily_headlines() {
    ob_start();

    $today = get_the_date('Y-m-d');
    $query = new WP_Query([
        'post_type'      => 'daily_headlines',
        'posts_per_page' => 100,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'date_query'     => [['after' => $today, 'inclusive' => true]],
    ]);

    if ($query->have_posts()) {
        echo '<div class="daily-headlines">';
        while ($query->have_posts()) {
            $query->the_post();
            $headline = get_the_title();
            $link     = get_field('headline_link');
            $source   = get_field('headline_source');

            echo '<div class="headline-item">';
            echo $link ? '<a href="' . esc_url($link) . '">' . esc_html($headline) . '</a>' : '<span>' . esc_html($headline) . '</span>';
            if ($source) echo ' <span class="source">(' . esc_html($source) . ')</span>';
            echo '</div>';
        }
        echo '</div>';
        wp_reset_postdata();
    } else {
        echo '<p>No headlines found.</p>';
    }

    return ob_get_clean();
}
add_shortcode('daily_headlines', 'display_daily_headlines');


// === Shortcode: Display Daily Headlines (Admin Only Text) ===
function display_daily_headlines_text() {
    if (!current_user_can('manage_options')) return '';

    ob_start();
    $today = get_the_date('Y-m-d');
    $query = new WP_Query([
        'post_type'      => 'daily_headlines',
        'posts_per_page' => 100,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'date_query'     => [['after' => $today, 'inclusive' => true]],
    ]);

    if ($query->have_posts()) {
        $output = "";
        while ($query->have_posts()) {
            $query->the_post();
            $headline = get_the_title();
            $link     = get_field('headline_link');
            $source   = get_field('headline_source');

            $line = $headline;
            if ($link)   $line .= " - $link";
            if ($source) $line .= " ($source)";
            $output .= $line . "\n";
        }
        wp_reset_postdata();
        echo '<pre class="daily-headlines-text" style="background:#f9f9f9; padding:10px;">' . esc_html($output) . '</pre>';
    }

    return ob_get_clean();
}
add_shortcode('daily_headlines_text', 'display_daily_headlines_text');


// === Helper Function: Get Trending Topics (OPTIMIZED WITH CACHING) ===
function govbrief_get_trending_topics($target_date = null) {
    if ($target_date === null) {
        $target_date = date('Y-m-d');
    }

    $cache_key = 'govbrief_trending_' . md5($target_date);
    $cached = get_transient($cache_key);
    if ($cached !== false) {
        return $cached;
    }

    $today_date = $target_date;
    $start_date = date('Y-m-d', strtotime($target_date . ' -6 days'));

    $trend_query = new WP_Query([
        'post_type'      => 'daily-headlines',
        'posts_per_page' => 200,
        'post_status'    => 'publish',
        'fields'         => 'ids',
        'meta_query'     => [[
            'key'     => 'headline_date',
            'value'   => [$start_date, $today_date],
            'compare' => 'BETWEEN',
            'type'    => 'DATE'
        ]]
    ]);

    $cat_ids = [];
    $tag_ids = [];

    if ($trend_query->have_posts()) {
        foreach ($trend_query->posts as $post_id) {
            $post_cats = wp_get_post_categories($post_id);
            if (!empty($post_cats)) {
                $cat_ids = array_merge($cat_ids, $post_cats);
            }

            $post_tags = wp_get_post_tags($post_id, array('fields' => 'ids'));
            if (!empty($post_tags)) {
                $tag_ids = array_merge($tag_ids, $post_tags);
            }
        }
        wp_reset_postdata();
    }

    $cat_counts = array_count_values($cat_ids);
    $tag_counts = array_count_values($tag_ids);

    arsort($cat_counts);
    arsort($tag_counts);

    $top_cat_ids = array_slice(array_keys($cat_counts), 0, 3);
    $top_tag_ids = array_slice(array_keys($tag_counts), 0, 6);

    $cat_names = [];
    $tag_names = [];

    foreach ($top_cat_ids as $cat_id) {
        $cat = get_category($cat_id);
        if ($cat) {
            $cat_names[] = $cat->name;
        }
    }

    foreach ($top_tag_ids as $tag_id) {
        $tag = get_tag($tag_id);
        if ($tag) {
            $tag_names[] = $tag->name;
        }
    }

    $result = [
        'categories' => $cat_names,
        'tags' => $tag_names
    ];

    set_transient($cache_key, $result, 6 * HOUR_IN_SECONDS);

    return $result;
}

// === Intensity Score Display Function v1 (Styled, with Emoji History) ===
function govbrief_intensity_display($atts = []) {
    global $post;

    $atts = shortcode_atts(['post_id' => null], $atts);
    $post_id = $atts['post_id'] ? intval($atts['post_id']) : ($post ? $post->ID : null);

    if (!$post_id) return '';

    $score = get_field('intensity_score', $post_id);
    $score = is_numeric($score) ? intval($score) : 100;

    if      ( $score < 85 ) {
        $emoji     = '🟢';
        $label     = 'Low activity. Things are quieter than usual.';
        $emoji_color = '#219653';
    }
    elseif  ( $score < 110 ) {
        $emoji     = '🟡';
        $label     = 'Normal range. Baseline political energy.';
        $emoji_color = '#b49f00';
    }
    elseif  ( $score < 130 ) {
        $emoji     = '🟠';
        $label     = 'Heated day. Volume above normal.';
        $emoji_color = '#f2994a';
    }
    elseif  ( $score < 150 ) {
        $emoji     = '🔴';
        $label     = 'High intensity. Big news cycle.';
        $emoji_color = '#eb5757';
    }
    else {
        $emoji     = '🚨';
        $label     = 'Extreme volume. Major political developments.';
        $emoji_color = '#eb5757';
    }

    $calendar_date = get_field('calendar_date', $post_id);
    if (!$calendar_date) {
        $calendar_date = get_the_date('Y-m-d', $post_id);
    }

    if (strlen($calendar_date) === 8 && is_numeric($calendar_date)) {
        $calendar_date = substr($calendar_date, 0, 4) . '-' . substr($calendar_date, 4, 2) . '-' . substr($calendar_date, 6, 2);
    }

    $cal_date_obj = new DateTime($calendar_date);
    $recent_scores = array();

    for ($i = 1; $i <= 5; $i++) {
        $prev_date = clone $cal_date_obj;
        $prev_date->modify("-{$i} days");
        $target_date = $prev_date->format('Ymd');

        $prev_query = new WP_Query(array(
            'post_type'      => 'post',
            'posts_per_page' => 1,
            'post_status'    => 'publish',
            'fields'         => 'ids',
            'meta_query'     => array(array(
                'key'     => 'calendar_date',
                'value'   => $target_date,
                'compare' => '=',
            )),
        ));

        if ($prev_query->have_posts()) {
            $prev_id = $prev_query->posts[0];
            $prev_score = get_field('intensity_score', $prev_id);
            $recent_scores[] = is_numeric($prev_score) ? intval($prev_score) : 100;
        } else {
            $recent_scores[] = 100;
        }
        wp_reset_postdata();
    }

    $out = '<div class="gb-intensity-container" style="background:#f8f9fa;border:4px solid #007cba;padding:16px;margin-bottom:24px;border-radius:8px;width:100%;max-width:800px;box-sizing:border-box;">';

    $out .= '<div style="font-weight:bold;font-size:1.25em;color:#007cba;margin-bottom:12px;border-bottom:2px solid #007cba;padding-bottom:8px;display:flex;align-items:center;gap:10px;">';
    $out .= '<span style="font-size:1.1em;">' . $emoji . '</span> GovBrief Intensity Score <span style="font-weight:700;color:#24292f;">' . $score . '</span>';
    $out .= '</div>';

    $out .= '<div style="margin-bottom:10px;"><span style="font-size:1.05rem;font-weight:500;color:' . $emoji_color . ';">' . $label . '</span></div>';

    if (count($recent_scores) > 1) {
        $out .= '<div style="margin-bottom:10px;font-size:1.03rem;font-weight:500;color:#222;">Previous 5 Days<br>';
        foreach ($recent_scores as $i => $rs) {
            if      ( $rs < 85 )     $emo = '🟢';
            elseif  ( $rs < 110 )    $emo = '🟡';
            elseif  ( $rs < 130 )    $emo = '🟠';
            elseif  ( $rs < 150 )    $emo = '🔴';
            else                     $emo = '🚨';

            $out .= '<span style="font-size:1.15em;font-weight:600;letter-spacing:1px;margin-right:3px;">' . $emo . '</span>';
            $out .= '<span style="font-size:1.02em;font-weight:600;color:#24292f;margin-right:10px;">' . $rs . '</span>';
            if ($i < count($recent_scores)-1) $out .= '<span style="color:#bbb;font-size:1.1em;margin-right:8px;">|</span>';
        }
        $out .= '</div>';
    }

    $out .= '<div style="margin-top:4px;font-size:0.97rem;color:#444;line-height:1.5;word-wrap:break-word;overflow-wrap:break-word;">';
    $out .= 'This is an indexed score based on news activity. <strong>100</strong> = our permanent historical baseline that is adjusted for weekdays and weekend days. Scores above 100 mean a busier-than-normal news day.';
    $out .= '</div>';

    $out .= '</div>';

    return $out;
}
add_shortcode('intensity-score', 'govbrief_intensity_display');


// === Trending Topics Display Box ===
function govbrief_trending_topics_box($atts = []) {
    global $post;

    $atts = shortcode_atts(['post_id' => null], $atts);
    $post_id = $atts['post_id'] ? intval($atts['post_id']) : ($post ? $post->ID : null);

    if (!$post_id) return '';

    $target_date = null;
    $hd_raw = get_field('calendar_date', $post_id) ?: get_the_date('Y-m-d', $post_id);

    $dt = DateTime::createFromFormat('F j, Y', $hd_raw);
    if (!$dt) {
        $dt = DateTime::createFromFormat('Y-m-d', $hd_raw);
    }
    if (!$dt) {
        $dt = new DateTime();
    }

    $target_date = $dt->format('Y-m-d');

    $trending = govbrief_get_trending_topics($target_date);
    $cat_names = $trending['categories'];
    $tag_names = $trending['tags'];

    $output = '<div class="gb-trending-container" style="background:#f8f9fa;border:4px solid #007cba;padding:16px;margin-bottom:24px;border-radius:8px;width:100%;max-width:800px;box-sizing:border-box;">';

    $output .= '<div class="trending-header" style="font-weight:bold;font-size:1.25em;color:#007cba;margin-bottom:12px;border-bottom:2px solid #007cba;padding-bottom:8px;">';
    $output .= '<span style="font-size:1.1em;">📈</span> What\'s Trending This Week';
    $output .= '</div>';

    $output .= '<div class="trending-explanation" style="font-size:0.9em;color:#666;margin-bottom:16px;line-height:1.4;word-wrap:break-word;overflow-wrap:break-word;">';
    $output .= 'Based on analysis of the past 7 days of headlines, these are the most frequently covered topics and categories.';
    $output .= '</div>';

    $output .= '<div class="trending-categories" style="margin-bottom:16px;">';
    $output .= '<div style="font-weight:bold;color:#333;margin-bottom:6px;">Story Categories</div>';

    if (!empty($cat_names)) {
        $output .= '<div class="category-tags" style="display:flex;flex-wrap:wrap;gap:8px;">';
        foreach ($cat_names as $category) {
            $output .= '<span class="category-tag" style="background:#007cba;color:white;padding:4px 12px;border-radius:16px;font-size:0.9em;font-weight:500;">';
            $output .= esc_html($category);
            $output .= '</span>';
        }
        $output .= '</div>';
    } else {
        $output .= '<span style="color:#999;font-style:italic;">No trending categories identified</span>';
    }
    $output .= '</div>';

    $output .= '<div class="trending-topics">';
    $output .= '<div style="font-weight:bold;color:#333;margin-bottom:6px;">Specific Topics</div>';

    if (!empty($tag_names)) {
        $output .= '<div class="topic-tags" style="display:flex;flex-wrap:wrap;gap:8px;">';
        foreach ($tag_names as $topic) {
            $output .= '<span class="topic-tag" style="background:#e8f4f8;color:#007cba;border:1px solid #007cba;padding:4px 12px;border-radius:16px;font-size:0.9em;">';
            $output .= esc_html($topic);
            $output .= '</span>';
        }
        $output .= '</div>';
    } else {
        $output .= '<span style="color:#999;font-style:italic;">No trending topics identified</span>';
    }
    $output .= '</div>';

    if ($target_date) {
        $end_date = DateTime::createFromFormat('Y-m-d', $target_date);
        $start_date = clone $end_date;
        $start_date->modify('-6 days');
        $footer_text = 'Analysis covers ' . $start_date->format('M j') . ' - ' . $end_date->format('M j, Y') . ' • Historical data';
    } else {
        $footer_text = 'Analysis covers ' . date('M j', strtotime('-6 days')) . ' - ' . date('M j, Y') . ' • Updates daily';
    }

    $output .= '<div class="trending-footer" style="margin-top:16px;padding-top:12px;border-top:1px solid #ddd;font-size:0.85em;color:#666;">';
    $output .= $footer_text;
    $output .= '</div>';

    $output .= '</div>';

    return $output;
}
add_shortcode('trending_topics_box', 'govbrief_trending_topics_box');


// === Quote Block Display ===
if (!function_exists('govbrief_quote_block')) {
    function govbrief_quote_block($atts = []) {
        global $post;

        $atts = shortcode_atts(['post_id' => null], $atts);
        $post_id = $atts['post_id'] ? intval($atts['post_id']) : ($post ? $post->ID : null);

        if (!$post_id) return '';

        $quote = function_exists('get_field') ? get_field('gbt_quote_text', $post_id) : '';
        $cite  = function_exists('get_field') ? get_field('gbt_quote_citation', $post_id) : '';

        if (($quote === '' || $quote === null) && ($cite === '' || $cite === null)) return '';

        $container_style = 'background:#fffdf8;padding:16px;margin:20px 0 24px;border-radius:8px;'
            . 'border:4px solid #f3e7d6;border-left:4px solid #d97706;box-shadow:0 1px 0 rgba(0,0,0,.06);'
            . 'width:100%;max-width:800px;box-sizing:border-box;';

        $label_style = 'font-weight:bold;font-size:1.25em;color:#b45309;margin-bottom:12px;'
            . 'border-bottom:2px solid #b45309;padding-bottom:8px;display:flex;align-items:center;gap:10px;';

        $quote_style = 'margin:0 0 6px 0;font-style:italic;color:#1f2937;line-height:1.55;';

        $cite_style = 'display:block;margin-top:4px;font-style:normal;font-size:.875rem;color:#6b7280;';

        $allowed_q = array('em'=>array(), 'i'=>array(), 'strong'=>array(), 'b'=>array(), 'br'=>array());
        $allowed_c = array('a'=>array('href'=>array(), 'target'=>array(), 'rel'=>array()),
                           'em'=>array(), 'i'=>array(), 'strong'=>array(), 'b'=>array(), 'br'=>array());

        $quote_html = $quote ? wp_kses($quote, $allowed_q) : '';
        $cite_html  = $cite  ? wp_kses($cite,  $allowed_c) : '';

        $out  = '<section class="gbt-quote" style="'. $container_style .'" aria-labelledby="gbt-quote-label">';
        $out .= '<span id="gbt-quote-label" style="'. $label_style .'">Today\'s Quote</span>';
        if ($quote_html !== '') $out .= '<div style="'. $quote_style .'">'. $quote_html .'</div>';
        if ($cite_html  !== '') $out .= '<cite style="'. $cite_style  .'">'. $cite_html  .'</cite>';
        $out .= '</section>';

        return $out;
    }
}

add_action('init', function () {
    if (!shortcode_exists('govbrief_quote')) {
        add_shortcode('govbrief_quote', function($atts){ return govbrief_quote_block($atts); });
    }
});


// === Yesterday's Most Read Display ===
if (!function_exists('govbrief_most_read_from_acf')) {
    function govbrief_most_read_from_acf($atts = []) {
        global $post;

        $atts = shortcode_atts(['post_id' => null], $atts);
        $post_id = $atts['post_id'] ? intval($atts['post_id']) : ($post ? $post->ID : null);

        if (!$post_id) return '';

        $blurb  = function_exists('get_field') ? get_field('gbt_mr_blurb', $post_id) : '';
        $url    = function_exists('get_field') ? get_field('gbt_mr_url',   $post_id) : '';
        $button = function_exists('get_field') ? get_field('gbt_mr_button',$post_id) : 'Catch Up';

        if (trim((string)$blurb) === '' && trim((string)$url) === '') return '';

        $container_style = 'background:#fffdf8;padding:16px;margin:20px 0 24px;border-radius:8px;'
            . 'border:4px solid #f3e7d6;border-left:4px solid #d97706;box-shadow:0 1px 0 rgba(0,0,0,.06);'
            . 'width:100%;max-width:800px;box-sizing:border-box;';

        $header_style = 'font-weight:bold;font-size:1.25em;color:#b45309;margin-bottom:12px;'
            . 'border-bottom:2px solid #b45309;padding-bottom:8px;';
        $blurb_style = 'margin:0 0 12px 0;color:#1f2937;line-height:1.55;font-weight:500;';
        $btn_wrap_style = 'display:flex;justify-content:flex-start;';
        $btn_style = 'display:inline-block;background:#007cba;color:#ffffff;text-decoration:none;'
            . 'padding:10px 14px;border-radius:6px;font-weight:600;';

        $blurb_html = $blurb ? wp_kses($blurb, array('strong'=>array(),'b'=>array(),'em'=>array(),'i'=>array(),'br'=>array())) : '';

        $raw_url = $url ? $url : '';
        $url_attr = '';
        if ($raw_url !== '') {
            $url_with_utm = add_query_arg(
                array(
                    'utm_source'   => 'govbrief',
                    'utm_medium'   => 'most_read',
                    'utm_campaign' => 'daily',
                ),
                $raw_url
            );
            $url_attr = esc_url($url_with_utm);
        }

        $btn_text = esc_html($button);

        $out  = '<section class="gbt-most-read" style="'. $container_style .'">';
        $out .= '<div class="gbt-most-read__header" style="'. $header_style .'">Yesterday\'s Most Read</div>';
        if ($blurb_html !== '') $out .= '<p class="gbt-most-read__blurb" style="'. $blurb_style .'">'. $blurb_html .'</p>';
        if ($url_attr !== '') {
            $out .= '<div class="gbt-most-read__cta" style="'. $btn_wrap_style .'">';
            $out .= '<a class="gbt-most-read__button" href="'. $url_attr .'" style="'. $btn_style .'" target="_blank" rel="noopener noreferrer" aria-label="Yesterday\'s Most Read">'. $btn_text .'</a>';
            $out .= '</div>';
        }
        $out .= '</section>';

        return $out;
    }
}

add_action('init', function () {
    if (!shortcode_exists('govbrief_most_read')) {
        add_shortcode('govbrief_most_read', function($atts){ return govbrief_most_read_from_acf($atts); });
    }
});


// === Sources Today Block ===
if (!function_exists('govbrief_sources_today_block')) {
    function govbrief_sources_today_block($atts) {
        global $post;
        if (!$post || !isset($post->ID)) return '';

        $a = shortcode_atts(array(
            'title'  => 'Sources',
            'format' => 'inline',
        ), $atts, 'govbrief_sources_today');

        $target_date = '';
        if (function_exists('get_field')) {
            $target_date = (string) get_field('calendar_date', $post->ID);
        }
        if ($target_date === '') {
            $target_date = get_the_date('Y-m-d', $post->ID);
        }
        if ($target_date === '') return '';

        $q = new WP_Query(array(
            'post_type'      => 'daily-headlines',
            'posts_per_page' => 100,
            'post_status'    => 'publish',
            'fields'         => 'ids',
            'meta_query'     => array(array(
                'key'     => 'headline_date',
                'value'   => $target_date,
                'compare' => '=',
                'type'    => 'DATE',
            )),
        ));
        if (!$q->have_posts()) return '';

        $seen = array();
        $sources = array();
        foreach ($q->posts as $hid) {
            $src = function_exists('get_field') ? get_field('headline_source', $hid) : '';
            if (!$src) continue;
            foreach (preg_split('/\r\n|\r|\n|,/', (string)$src) as $p) {
                $t = trim($p);
                if ($t === '') continue;
                $key = function_exists('mb_strtolower') ? mb_strtolower($t) : strtolower($t);
                if (isset($seen[$key])) continue;
                $seen[$key] = true;
                $sources[] = $t;
            }
        }
        if (empty($sources)) return '';

        $container_style = 'background:#f8f9fa;padding:12px 16px;margin:20px 0 24px;border-radius:8px;'
            . 'border:1px solid #e5e7eb;width:100%;max-width:800px;box-sizing:border-box;color:#374151;';
        $title_style = 'font-weight:600;font-size:1rem;margin:0 0 6px 0;color:#111827;';
        $inline_style = 'margin:0;font-size:.95rem;line-height:1.5;color:#4b5563;';
        $list_style = 'margin:0 0 0 1.2em;padding:0;font-size:.95rem;line-height:1.5;color:#4b5563;';

        $out  = '<section class="gbt-sources-today" style="'.$container_style.'">';
        $out .= '<div class="gbt-sources-today__title" style="'.$title_style.'">'.esc_html($a['title']).'</div>';

        if (strtolower($a['format']) === 'list') {
            $out .= '<ul class="gbt-sources-today__list" style="'.$list_style.'">';
            foreach ($sources as $it) $out .= '<li>'.esc_html($it).'</li>';
            $out .= '</ul>';
        } else {
            $out .= '<p class="gbt-sources-today__inline" style="'.$inline_style.'">'.esc_html(implode(', ', $sources)).'</p>';
        }

        $out .= '</section>';
        return $out;
    }
}
add_action('init', function () {
    if (!shortcode_exists('govbrief_sources_today')) {
        add_shortcode('govbrief_sources_today', 'govbrief_sources_today_block');
    }
});


// === Daily Post Calendar (OPTIMIZED - removed nested queries) ===
function daily_post_calendar_shortcode( $atts ) {
    $tag_slug = 'daily-post';
    $year  = isset( $_GET['dp_year'] ) ? intval( $_GET['dp_year'] ) : date( 'Y' );
    $month = isset( $_GET['dp_month'] ) ? intval( $_GET['dp_month'] ) : date( 'n' );
    $first_day_timestamp = mktime( 0, 0, 0, $month, 1, $year );
    $days_in_month = date( 't', $first_day_timestamp );
    global $wp_locale;

    $output = '<div class="daily-post-calendar">';

    $prev_year  = $month == 1 ? $year - 1 : $year;
    $prev_month = $month == 1 ? 12 : $month - 1;
    $next_year  = $month == 12 ? $year + 1 : $year;
    $next_month = $month == 12 ? 1 : $month + 1;

    $base_url = get_permalink();
    $prev_url = add_query_arg( array( 'dp_year' => $prev_year, 'dp_month' => $prev_month ), $base_url );
    $next_url = add_query_arg( array( 'dp_year' => $next_year, 'dp_month' => $next_month ), $base_url );

    $output .= '<div class="calendar-navigation">';
    $output .= '<a class="prev-month" href="' . esc_url( $prev_url ) . '">&laquo; Previous</a> ';
    $output .= '<span class="current-month">' . date( 'F Y', $first_day_timestamp ) . '</span> ';
    $output .= '<a class="next-month" href="' . esc_url( $next_url ) . '">Next &raquo;</a>';
    $output .= '</div>';

    $all_month_posts = get_posts(array(
        'year'           => $year,
        'monthnum'       => $month,
        'tag'            => $tag_slug,
        'posts_per_page' => 100,
        'orderby'        => 'date',
        'order'          => 'ASC'
    ));

    $posts_by_day = array();
    foreach($all_month_posts as $p) {
        $day = date('j', strtotime($p->post_date));
        if(!isset($posts_by_day[$day])) {
            $posts_by_day[$day] = array();
        }
        $posts_by_day[$day][] = $p;
    }

    $output .= '<table class="daily-post-calendar-table">';
    $output .= '<thead><tr>';
    $start_of_week = get_option( 'start_of_week' );
    for ( $i = 0; $i < 7; $i++ ) {
        $day_index = ( $i + $start_of_week ) % 7;
        $output .= '<th>' . esc_html( $wp_locale->get_weekday_abbrev( $wp_locale->get_weekday( $day_index ) ) ) . '</th>';
    }
    $output .= '</tr></thead><tbody><tr>';

    $first_day_of_week = date( 'w', $first_day_timestamp );
    $empty_cells = ( $first_day_of_week - $start_of_week + 7 ) % 7;
    for ( $i = 0; $i < $empty_cells; $i++ ) {
        $output .= '<td class="empty"></td>';
    }

    $day_counter = $empty_cells;

    for ( $day = 1; $day <= $days_in_month; $day++ ) {
        if ( $day_counter % 7 == 0 && $day_counter != 0 ) {
            $output .= '</tr><tr>';
        }

        $cell_content = '<div class="day-number">' . $day . '</div>';

        if ( isset($posts_by_day[$day]) && !empty($posts_by_day[$day]) ) {
            $cell_content .= '<ul class="post-list">';
            foreach($posts_by_day[$day] as $post) {
                $cell_content .= '<li><a href="' . esc_url( get_permalink($post->ID) ) . '">' . get_the_title($post->ID) . '</a></li>';
            }
            $cell_content .= '</ul>';

            $cell_content .= '<ul class="post-list-two-words">';
            foreach($posts_by_day[$day] as $post) {
                $title = get_the_title($post->ID);
                $words = preg_split('/\s+/', trim($title), 3);
                $first_two_words = implode(' ', array_slice($words, 0, 2));
                if (count($words) > 2) {
                    $first_two_words .= '...';
                }
                $cell_content .= '<li><a href="' . esc_url( get_permalink($post->ID) ) . '">' . $first_two_words . '</a></li>';
            }
            $cell_content .= '</ul>';

            $output .= '<td class="day has-post">' . $cell_content . '</td>';
        } else {
            $output .= '<td class="day no-post">' . $cell_content . '</td>';
        }

        $day_counter++;
    }

    while ( $day_counter % 7 != 0 ) {
        $output .= '<td class="empty"></td>';
        $day_counter++;
    }

    $output .= '</tr></tbody></table>';
    $output .= '</div>';

    return $output;
}
add_shortcode( 'daily_post_calendar', 'daily_post_calendar_shortcode' );


// === Category Colors (shared) ===
function govbrief_get_category_colors() {
    return [
        'Extremism' => '#dc2626', 'Dissent' => '#ea580c', 'Disaster Relief' => '#f59e0b',
        'Foreign Relations' => '#2563eb', 'War' => '#7c2d12', 'Health' => '#16a34a',
        'Human Rights' => '#db2777', 'Epstein Sex Crime Network' => '#7f1d1d',
        'Environment' => '#059669', 'Science' => '#0891b2',
        'DEI' => '#7c3aed', 'Voting Rights' => '#4f46e5', 'Censorship' => '#dc2626',
        'Economy' => '#16a34a', 'Military' => '#475569', 'Intelligence' => '#1e293b',
        'Justice Dept' => '#4338ca', 'Courts' => '#7c3aed', 'Criminal Justice' => '#be123c', 'Social Security' => '#0d9488',
        'Immigration' => '#ea580c', 'Education' => '#2563eb', 'Oversight' => '#64748b',
        'Congress' => '#1e40af', 'Federal Personnel' => '#6366f1', 'Transportation' => '#0891b2',
        'Data' => '#6b7280', 'Propaganda' => '#dc2626', 'Religion' => '#7c3aed',
        'Media' => '#059669', 'Arts' => '#db2777', 'Grift' => '#b91c1c',
        'Protest' => '#ea580c', 'Fighting Back' => '#16a34a'
    ];
}

function govbrief_get_categories_order() {
    return [
        'Extremism', 'Dissent', 'Disaster Relief', 'Foreign Relations', 'War',
        'Health', 'Human Rights', 'Epstein Sex Crime Network', 'Environment', 'Science', 'DEI',
        'Voting Rights', 'Censorship', 'Economy', 'Military', 'Intelligence',
        'Justice Dept', 'Courts', 'Criminal Justice', 'Social Security', 'Immigration', 'Education',
        'Oversight', 'Congress', 'Federal Personnel', 'Transportation', 'Data',
        'Propaganda', 'Religion', 'Media', 'Arts', 'Grift', 'Protest', 'Fighting Back'
    ];
}


// === GovBrief Cards Shortcode (OPTIMIZED with caching) ===
function govbrief_cards_shortcode($atts) {
    $post_date = get_field('calendar_date');

    if(!$post_date) return '<p>No calendar date set for this post.</p>';

    $cache_key = 'govbrief_cards_' . md5($post_date);
    $cached = get_transient($cache_key);
    if($cached !== false) {
        return $cached;
    }

    $categories = govbrief_get_categories_order();
    $category_colors = govbrief_get_category_colors();

    ob_start();

    $all_posts = get_posts([
        'post_type' => 'daily-headlines',
        'posts_per_page' => 200,
        'orderby' => 'menu_order title',
        'order' => 'ASC',
        'meta_query' => [[
            'key' => 'headline_date',
            'value' => $post_date,
            'compare' => '=',
            'type' => 'DATE'
        ]]
    ]);

    $national_headlines = [];
    foreach($all_posts as $headline) {
        $editions = get_field('include_in_editions', $headline->ID);

        $is_national = false;
        if(is_array($editions) && in_array('national', $editions)) {
            $is_national = true;
        } elseif($editions === 'national') {
            $is_national = true;
        }

        if($is_national) {
            $national_headlines[] = $headline;
        }
    }

    if(empty($national_headlines)) {
        return '<p>No national headlines found for ' . date('F j, Y', strtotime($post_date)) . '</p>';
    }

    $headlines_by_category = [];
    foreach($national_headlines as $headline) {
        $primary_cat_id = get_post_meta($headline->ID, '_yoast_wpseo_primary_category', true);
        if($primary_cat_id) {
            $primary_cat = get_category($primary_cat_id);
            if($primary_cat) {
                $cat_name = $primary_cat->name;
                if(!isset($headlines_by_category[$cat_name])) {
                    $headlines_by_category[$cat_name] = [];
                }
                $headlines_by_category[$cat_name][] = $headline;
            }
        } else {
            $post_categories = wp_get_post_categories($headline->ID);
            if(!empty($post_categories)) {
                $first_cat = get_category($post_categories[0]);
                if($first_cat) {
                    $cat_name = $first_cat->name;
                    if(!isset($headlines_by_category[$cat_name])) {
                        $headlines_by_category[$cat_name] = [];
                    }
                    $headlines_by_category[$cat_name][] = $headline;
                }
            }
        }
    }

    $total_stories = count($national_headlines);

    ?>
    <div class="govbrief-cards-section">
        <div class="cards-container">
            <?php
            $counter = 1;

            foreach($categories as $category) {
                if(!isset($headlines_by_category[$category])) continue;

                foreach($headlines_by_category[$category] as $headline) {
                    $title = $headline->post_title;
                    $link = get_field('headline_link', $headline->ID);
                    $source = get_field('headline_source', $headline->ID);
                    $callout = get_field('story_callout', $headline->ID);

                    $color = $category_colors[$category] ?? '#6b7280';

                    $date_display = date('F j, Y', strtotime($post_date));
                    ?>
                    <div class="story-card">
                        <div class="category-bar" style="background: <?php echo $color; ?>;">
                            <span><?php echo $category; ?></span>
                            <span class="story-number"><?php echo $counter; ?> of <?php echo $total_stories; ?></span>
                        </div>
                        <div class="story-content">
                            <h3><a href="<?php echo esc_url($link); ?>" target="_blank" rel="noopener"><?php echo esc_html($title); ?></a></h3>

                            <?php if($callout): ?>
                                <div class="callout-box"><?php echo esc_html($callout); ?></div>
                            <?php endif; ?>

                            <?php if($source): ?>
                                <p class="card-source">Source: <?php echo esc_html($source); ?></p>
                            <?php endif; ?>

                            <p class="card-date"><?php echo $date_display; ?></p>
                        </div>
                    </div>
                    <?php
                    $counter++;
                }
            }
            ?>
        </div>
    </div>

    <style>
    .govbrief-cards-section { margin: 40px 0; }
    .cards-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 20px;
        max-width: 1200px;
        margin: 0 auto;
    }
    @media (max-width: 768px) {
        .cards-container { grid-template-columns: 1fr; }
    }
    .story-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        overflow: hidden;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .story-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(0,0,0,0.15);
    }
    .category-bar {
        padding: 12px 15px;
        color: white;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 11px;
        letter-spacing: 1px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .story-number { opacity: 0.9; }
    .story-content { padding: 20px; }
    .story-content h3 {
        margin: 0 0 15px 0;
        font-size: 18px;
        line-height: 1.4;
    }
    .story-content h3 a {
        color: #1a1a1a;
        text-decoration: none;
    }
    .story-content h3 a:hover { color: #2563eb; }
    .callout-box {
        background: #f3f4f6;
        border-left: 4px solid #2563eb;
        padding: 12px 15px;
        margin: 15px 0;
        font-weight: 600;
        color: #1f2937;
        font-size: 15px;
    }
    .card-source {
        color: #6b7280;
        font-size: 14px;
        margin: 10px 0 0 0;
        font-style: italic;
    }
    .card-date {
        color: #9ca3af;
        font-size: 13px;
        margin: 10px 0 0 0;
        text-align: left;
    }
    </style>
    <?php

    $output = ob_get_clean();

    set_transient($cache_key, $output, 6 * HOUR_IN_SECONDS);

    return $output;
}
add_shortcode('govbrief_cards', 'govbrief_cards_shortcode');


// === Defining Moments Archive Shortcode ===
function govbrief_defining_moments_shortcode($atts) {
    $atts = shortcode_atts([
        'year' => date('Y')
    ], $atts);

    $year = intval($atts['year']);
    $category_colors = govbrief_get_category_colors();

    // Query all L3 headlines for the year
    $dm_posts = get_posts([
        'post_type' => 'daily-headlines',
        'posts_per_page' => -1,
        'meta_query' => [
            'relation' => 'AND',
            [
                'key' => 'severity_level',
                'value' => '3',
                'compare' => '='
            ],
            [
                'key' => 'headline_date',
                'value' => [$year . '-01-01', $year . '-12-31'],
                'compare' => 'BETWEEN',
                'type' => 'DATE'
            ]
        ],
        'orderby' => 'meta_value',
        'meta_key' => 'headline_date',
        'order' => 'ASC'
    ]);

    if (empty($dm_posts)) {
        return '<p>No defining moments found for ' . $year . '.</p>';
    }

    // Group by month
    $by_month = [];
    foreach ($dm_posts as $post) {
        $headline_date = get_field('headline_date', $post->ID);
        $month_key = date('Y-m', strtotime($headline_date));
        if (!isset($by_month[$month_key])) {
            $by_month[$month_key] = [];
        }
        $by_month[$month_key][] = $post;
    }

    // Sort months reverse chronological (newest first)
    krsort($by_month);

    $total = count($dm_posts);

    ob_start();
    ?>
    <div class="govbrief-defining-moments">
        <?php
        $counter = 1;
        foreach ($by_month as $month_key => $posts) {
            $month_label = date('F Y', strtotime($month_key . '-01'));
            ?>
            <h2 class="dm-month-header"><?php echo $month_label; ?></h2>
            <div class="cards-container">
                <?php
                foreach ($posts as $post) {
                    $title = $post->post_title;
                    $link = get_field('headline_link', $post->ID);
                    $source = get_field('headline_source', $post->ID);
                    $callout = get_field('story_callout', $post->ID);
                    $headline_date = get_field('headline_date', $post->ID);

                    // Get primary category
                    $category = 'Uncategorized';
                    $primary_cat_id = get_post_meta($post->ID, '_yoast_wpseo_primary_category', true);
                    if ($primary_cat_id) {
                        $primary_cat = get_category($primary_cat_id);
                        if ($primary_cat) {
                            $category = $primary_cat->name;
                        }
                    } else {
                        $post_categories = wp_get_post_categories($post->ID);
                        if (!empty($post_categories)) {
                            $first_cat = get_category($post_categories[0]);
                            if ($first_cat) {
                                $category = $first_cat->name;
                            }
                        }
                    }

                    $color = $category_colors[$category] ?? '#6b7280';
                    $date_display = date('F j, Y', strtotime($headline_date));
                    ?>
                    <div class="story-card">
                        <div class="category-bar" style="background: <?php echo $color; ?>;">
                            <span><?php echo esc_html($category); ?></span>
                            <span class="story-number"><?php echo $counter; ?> of <?php echo $total; ?></span>
                        </div>
                        <div class="story-content">
                            <h3><a href="<?php echo esc_url($link); ?>" target="_blank" rel="noopener"><?php echo esc_html($title); ?></a></h3>

                            <?php if ($callout): ?>
                                <div class="callout-box"><?php echo esc_html($callout); ?></div>
                            <?php endif; ?>

                            <?php if ($source): ?>
                                <p class="card-source">Source: <?php echo esc_html($source); ?></p>
                            <?php endif; ?>

                            <p class="card-date"><?php echo $date_display; ?></p>
                        </div>
                    </div>
                    <?php
                    $counter++;
                }
                ?>
            </div>
            <?php
        }
        ?>
    </div>

    <style>
    .govbrief-defining-moments { margin: 40px 0; }
    .dm-month-header {
        font-size: 28px;
        font-weight: 700;
        color: #1a1a1a;
        margin: 40px 0 20px 0;
        padding-bottom: 10px;
        border-bottom: 3px solid #007cba;
    }
    .dm-month-header:first-child { margin-top: 0; }

    /* Card styles - matching govbrief_cards exactly */
    .govbrief-defining-moments .cards-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 20px;
        max-width: 1200px;
        margin: 0 auto;
    }
    @media (max-width: 768px) {
        .govbrief-defining-moments .cards-container { grid-template-columns: 1fr; }
    }
    .govbrief-defining-moments .story-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        overflow: hidden;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .govbrief-defining-moments .story-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(0,0,0,0.15);
    }
    .govbrief-defining-moments .category-bar {
        padding: 12px 15px;
        color: white;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 11px;
        letter-spacing: 1px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .govbrief-defining-moments .story-number { opacity: 0.9; }
    .govbrief-defining-moments .story-content { padding: 20px; }
    .govbrief-defining-moments .story-content h3 {
        margin: 0 0 15px 0;
        font-size: 18px;
        line-height: 1.4;
    }
    .govbrief-defining-moments .story-content h3 a {
        color: #1a1a1a;
        text-decoration: none;
    }
    .govbrief-defining-moments .story-content h3 a:hover { color: #2563eb; }
    .govbrief-defining-moments .callout-box {
        background: #f3f4f6;
        border-left: 4px solid #2563eb;
        padding: 12px 15px;
        margin: 15px 0;
        font-weight: 600;
        color: #1f2937;
        font-size: 15px;
    }
    .govbrief-defining-moments .card-source {
        color: #6b7280;
        font-size: 14px;
        margin: 10px 0 0 0;
        font-style: italic;
    }
    .govbrief-defining-moments .card-date {
        color: #9ca3af;
        font-size: 13px;
        margin: 10px 0 0 0;
        text-align: left;
    }
    </style>
    <?php

    return ob_get_clean();
}
add_shortcode('defining_moments', 'govbrief_defining_moments_shortcode');


// === Homepage Mini-Cards Shortcode (OPTIMIZED with caching) ===
function govbrief_homepage_cards_shortcode($atts) {
    $atts = shortcode_atts([
        'count' => 6
    ], $atts);

    $cache_key = 'govbrief_homepage_cards_' . $atts['count'];
    $cached = get_transient($cache_key);
    if($cached !== false) {
        return $cached;
    }

    $category_colors = govbrief_get_category_colors();

    ob_start();

    $all_posts = get_posts([
        'post_type' => 'daily-headlines',
        'posts_per_page' => $atts['count'] * 3,
        'orderby' => 'date',
        'order' => 'DESC'
    ]);

    $national_headlines = [];
    foreach($all_posts as $headline) {
        if(count($national_headlines) >= $atts['count']) break;

        $editions = get_field('include_in_editions', $headline->ID);

        $is_national = false;
        if(is_array($editions) && in_array('national', $editions)) {
            $is_national = true;
        } elseif($editions === 'national') {
            $is_national = true;
        }

        if($is_national) {
            $national_headlines[] = $headline;
        }
    }

    if(empty($national_headlines)) {
        return '<p>No recent headlines found.</p>';
    }

    $most_recent_date = get_field('headline_date', $national_headlines[0]->ID);
    $total_count_for_day = 0;

    if($most_recent_date) {
        $all_day_headlines = get_posts([
            'post_type' => 'daily-headlines',
            'posts_per_page' => -1,
            'meta_query' => [[
                'key' => 'headline_date',
                'value' => $most_recent_date,
                'compare' => '=',
                'type' => 'DATE'
            ]]
        ]);

        foreach($all_day_headlines as $day_headline) {
            $editions = get_field('include_in_editions', $day_headline->ID);
            $is_national = false;
            if(is_array($editions) && in_array('national', $editions)) {
                $is_national = true;
            } elseif($editions === 'national') {
                $is_national = true;
            }
            if($is_national) {
                $total_count_for_day++;
            }
        }
    }

    if($total_count_for_day == 0) {
        $total_count_for_day = count($national_headlines);
    }

    $latest_daily_post = get_posts([
        'post_type' => 'post',
        'posts_per_page' => 1,
        'post_status' => 'publish',
        'meta_key' => 'calendar_date',
        'orderby' => 'meta_value',
        'order' => 'DESC',
        'meta_type' => 'DATE'
    ]);

    $daily_post_link = '';
    if(!empty($latest_daily_post)) {
        $daily_post_link = get_permalink($latest_daily_post[0]->ID);
    }

    ?>
    <div class="govbrief-homepage-cards">
        <div class="homepage-cards-grid">
            <?php
            $counter = 1;

            foreach($national_headlines as $headline) {
                $title = $headline->post_title;
                $link = get_field('headline_link', $headline->ID);
                $callout = get_field('story_callout', $headline->ID);
                $source = get_field('headline_source', $headline->ID);
                $headline_date = get_field('headline_date', $headline->ID);

                $category = 'News';
                $color = '#6b7280';

                $primary_cat_id = get_post_meta($headline->ID, '_yoast_wpseo_primary_category', true);
                if($primary_cat_id) {
                    $primary_cat = get_category($primary_cat_id);
                    if($primary_cat) {
                        $category = $primary_cat->name;
                        $color = $category_colors[$category] ?? '#6b7280';
                    }
                } else {
                    $post_categories = wp_get_post_categories($headline->ID);
                    if(!empty($post_categories)) {
                        $first_cat = get_category($post_categories[0]);
                        if($first_cat) {
                            $category = $first_cat->name;
                            $color = $category_colors[$category] ?? '#6b7280';
                        }
                    }
                }
                ?>
                <a href="<?php echo esc_url($link); ?>" class="mini-card" target="_blank" rel="noopener">
                    <div class="mini-category-bar" style="background: <?php echo $color; ?>;">
                        <span><?php echo esc_html($category); ?></span>
                        <span class="story-number"><?php echo $counter; ?> of <?php echo $total_count_for_day; ?></span>
                    </div>
                    <div class="mini-card-content">
                        <h3 class="mini-card-title"><?php echo esc_html($title); ?></h3>
                        <?php if($callout): ?>
                            <div class="mini-callout"><?php echo esc_html($callout); ?></div>
                        <?php endif; ?>

                        <?php if($source): ?>
                            <p class="card-source">Source: <?php echo esc_html($source); ?></p>
                        <?php endif; ?>

                        <?php if($headline_date): ?>
                            <p class="card-date"><?php echo date('F j, Y', strtotime($headline_date)); ?></p>
                        <?php endif; ?>
                    </div>
                </a>
                <?php
                $counter++;
            }
            ?>
        </div>

        <?php if($daily_post_link): ?>
        <div class="homepage-cards-button">
            <a href="<?php echo esc_url($daily_post_link); ?>" class="view-full-brief-btn">
                View Today's Full Brief →
            </a>
        </div>
        <?php endif; ?>
    </div>

    <style>
    .govbrief-homepage-cards { margin: 30px 0; }
    .homepage-cards-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        max-width: 1200px;
        margin: 0 auto;
    }
    .mini-card {
        background: white;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        overflow: hidden;
        transition: transform 0.2s, box-shadow 0.2s;
        display: block;
        text-decoration: none;
        color: inherit;
    }
    .mini-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .mini-category-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 12px;
        color: white;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 10px;
        letter-spacing: 0.5px;
    }
    .mini-category-bar .story-number {
        font-size: 9px;
        opacity: 0.9;
    }
    .mini-card-content { padding: 15px; }
    .mini-card-title {
        margin: 0 0 12px 0;
        font-size: 15px;
        line-height: 1.4;
        font-weight: 600;
        color: #1a1a1a;
    }
    .mini-callout {
        background: #f3f4f6;
        border-left: 3px solid #2563eb;
        padding: 8px 10px;
        margin: 12px 0 0 0;
        font-size: 13px;
        font-weight: 600;
        color: #1f2937;
    }
    .card-source {
        color: #6b7280;
        font-size: 13px;
        margin: 10px 0 0 0;
        font-style: italic;
    }
    .card-date {
        color: #9ca3af;
        font-size: 12px;
        margin: 5px 0 0 0;
    }
    .homepage-cards-button {
        text-align: center;
        margin: 30px 0 0 0;
    }
    .view-full-brief-btn {
        display: inline-block;
        background: #ea580c;
        color: white;
        padding: 12px 28px;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 600;
        font-size: 16px;
        transition: background 0.2s;
    }
    .view-full-brief-btn:hover { background: #c2410c; }
    @media (max-width: 900px) {
        .homepage-cards-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }
    }
    @media (max-width: 640px) {
        .homepage-cards-grid {
            grid-template-columns: 1fr;
            gap: 15px;
        }
        .mini-card-title { font-size: 16px; }
        .mini-card-content { padding: 16px; }
        .view-full-brief-btn {
            padding: 14px 32px;
            font-size: 17px;
        }
    }
    </style>
    <?php

    $output = ob_get_clean();

    set_transient($cache_key, $output, 30 * MINUTE_IN_SECONDS);

    return $output;
}
add_shortcode('govbrief_homepage_cards', 'govbrief_homepage_cards_shortcode');


// ========================================
// GOVBRIEF INTENSITY SCORE v2: SEVERITY-WEIGHTED
// ========================================

// === Severity Weight Map ===
function govbrief_get_severity_weight($level) {
    $weights = [
        1 => 1,  // Routine
        2 => 3,  // New Normal
        3 => 5,  // Defining Moment
    ];
    return isset($weights[$level]) ? $weights[$level] : 1;
}

// === Calculate Weighted Intensity for a Date ===
function govbrief_calculate_weighted_intensity($target_date) {
    $headlines = get_posts([
        'post_type' => 'daily-headlines',
        'posts_per_page' => 200,
        'post_status' => 'publish',
        'meta_query' => [[
            'key' => 'headline_date',
            'value' => $target_date,
            'compare' => '=',
            'type' => 'DATE'
        ]]
    ]);

    if (empty($headlines)) {
        return [
            'score' => 0,
            'volume' => 0,
            'weighted_total' => 0,
            'avg_severity' => 0,
            'defining_moments' => 0,
            'level_counts' => [1 => 0, 2 => 0, 3 => 0],
        ];
    }

    $volume = count($headlines);
    $weighted_total = 0;
    $defining_moments = 0;
    $level_counts = [1 => 0, 2 => 0, 3 => 0];

    foreach ($headlines as $headline) {
        $level = get_field('severity_level', $headline->ID);
        $level = $level ? intval($level) : 1;

        $weight = govbrief_get_severity_weight($level);
        $weighted_total += $weight;

        $level_counts[$level]++;

        if ($level == 3) {
            $defining_moments++;
        }
    }

    $avg_severity = $volume > 0 ? round($weighted_total / $volume, 2) : 0;

    $day_of_week = date('l', strtotime($target_date));
    $is_weekend_adjusted = in_array($day_of_week, ['Saturday', 'Sunday', 'Monday']);

    $weighted_baseline = $is_weekend_adjusted ? 16.5 : 25;

    $score = $weighted_baseline > 0 ? round(($weighted_total / $weighted_baseline) * 100) : 100;

    return [
        'score' => $score,
        'volume' => $volume,
        'weighted_total' => $weighted_total,
        'avg_severity' => $avg_severity,
        'defining_moments' => $defining_moments,
        'level_counts' => $level_counts,
        'is_weekend' => $is_weekend_adjusted,
    ];
}

// === Volume Label ===
function govbrief_volume_label($count) {
    if ($count < 13) return 'Low';
    if ($count <= 20) return 'Normal';
    if ($count <= 25) return 'High';
    return 'Extreme';
}

// === Volume Color ===
function govbrief_volume_color($count) {
    if ($count < 13) return ['bg' => '#fef3c7', 'text' => '#92400e'];
    if ($count <= 20) return ['bg' => '#d1fae5', 'text' => '#065f46'];
    if ($count <= 25) return ['bg' => '#ffedd5', 'text' => '#9a3412'];
    return ['bg' => '#fee2e2', 'text' => '#991b1b'];
}

// === Severity Label ===
function govbrief_severity_label($avg) {
    if ($avg < 1.5) return 'Low';
    if ($avg < 2.5) return 'Moderate';
    if ($avg < 4.0) return 'High';
    return 'Critical';
}

// === Severity Color ===
function govbrief_severity_color($avg) {
    if ($avg < 1.5) return ['bg' => '#d1fae5', 'text' => '#065f46'];
    if ($avg < 2.5) return ['bg' => '#fef3c7', 'text' => '#92400e'];
    if ($avg < 4.0) return ['bg' => '#ffedd5', 'text' => '#9a3412'];
    return ['bg' => '#fee2e2', 'text' => '#991b1b'];
}

// === Defining Moments Color ===
function govbrief_dm_color($count) {
    if ($count == 0) return ['bg' => '#d1fae5', 'text' => '#065f46'];
    if ($count <= 2) return ['bg' => '#fef3c7', 'text' => '#92400e'];
    return ['bg' => '#fee2e2', 'text' => '#991b1b'];
}

// === Weather Report Shortcode (v2 Intensity) ===
function govbrief_weather_report_shortcode($atts = []) {
    global $post;

    $atts = shortcode_atts(['post_id' => null], $atts);
    $post_id = $atts['post_id'] ? intval($atts['post_id']) : ($post ? $post->ID : null);

    if (!$post_id) return '';

    $calendar_date = get_field('calendar_date', $post_id);
    if (!$calendar_date) {
        $calendar_date = get_the_date('Y-m-d', $post_id);
    }

    if (strlen($calendar_date) === 8 && is_numeric($calendar_date)) {
        $calendar_date = substr($calendar_date, 0, 4) . '-' . substr($calendar_date, 4, 2) . '-' . substr($calendar_date, 6, 2);
    }

    $data = govbrief_calculate_weighted_intensity($calendar_date);
    $score = $data['score'];

    if ($score < 85) {
        $emoji = '🟢';
        $label = 'Quiet Day. Weekend and holiday level.';
        $emoji_color = '#219653';
    } elseif ($score < 110) {
        $emoji = '🟡';
        $label = 'Typical range for this administration.';
        $emoji_color = '#b49f00';
    } elseif ($score < 130) {
        $emoji = '🟠';
        $label = 'Elevated Activity. Watch for distractions.';
        $emoji_color = '#f2994a';
    } elseif ($score < 150) {
        $emoji = '🔴';
        $label = 'High Intensity. They\'re flooding the zone.';
        $emoji_color = '#eb5757';
    } else {
        $emoji = '🚨';
        $label = 'Extreme. History will remember.';
        $emoji_color = '#eb5757';
    }

    $recent_scores = [];
    $cal_date_obj = new DateTime($calendar_date);

    for ($i = 1; $i <= 5; $i++) {
        $prev_date = clone $cal_date_obj;
        $prev_date->modify("-{$i} days");
        $prev_data = govbrief_calculate_weighted_intensity($prev_date->format('Y-m-d'));
        $recent_scores[] = $prev_data['score'];
    }

    $out = '<div class="gb-intensity-container" style="background:#f8f9fa;border:4px solid #007cba;padding:16px;margin-bottom:24px;border-radius:8px;width:100%;max-width:800px;box-sizing:border-box;">';

    $out .= '<div style="font-weight:bold;font-size:1.25em;color:#007cba;margin-bottom:12px;border-bottom:2px solid #007cba;padding-bottom:8px;display:flex;align-items:center;gap:10px;">';
    $out .= '<span style="font-size:1.1em;">' . $emoji . '</span> GovBrief Intensity Score <span style="font-weight:700;color:#24292f;">' . $score . '</span>';
    $out .= '</div>';

    $out .= '<div style="margin-bottom:14px;"><span style="font-size:1.05rem;font-weight:500;color:' . $emoji_color . ';">' . $label . '</span></div>';

    $vol_color = govbrief_volume_color($data['volume']);
    $sev_color = govbrief_severity_color($data['avg_severity']);
    $dm_color = govbrief_dm_color($data['defining_moments']);

    $out .= '<div style="display:flex;flex-wrap:wrap;gap:12px;margin-bottom:14px;font-size:0.95rem;">';

    $out .= '<div style="background:' . $vol_color['bg'] . ';color:' . $vol_color['text'] . ';padding:8px 12px;border-radius:6px;font-weight:600;">';
    $out .= 'Volume: ' . govbrief_volume_label($data['volume']) . ' (' . $data['volume'] . ')';
    $out .= '</div>';

    $out .= '<div style="background:' . $sev_color['bg'] . ';color:' . $sev_color['text'] . ';padding:8px 12px;border-radius:6px;font-weight:600;">';
    $out .= 'Severity: ' . govbrief_severity_label($data['avg_severity']) . ' (' . $data['avg_severity'] . ')';
    $out .= '</div>';

    $out .= '<div style="background:' . $dm_color['bg'] . ';color:' . $dm_color['text'] . ';padding:8px 12px;border-radius:6px;font-weight:600;">';
    $out .= 'Defining Moments: ' . $data['defining_moments'];
    $out .= '</div>';

    $out .= '</div>';

    // Display Defining Moments (L3 headlines) if any exist
    if ($data['defining_moments'] > 0) {
        // Query for L3 headlines from this date
        $dm_args = [
            'post_type' => 'daily-headlines',
            'posts_per_page' => -1,
            'meta_query' => [
                'relation' => 'AND',
                [
                    'key' => 'headline_date',
                    'value' => $calendar_date,
                    'compare' => '=',
                    'type' => 'DATE'
                ],
                [
                    'key' => 'severity_level',
                    'value' => '3',
                    'compare' => '='
                ]
            ],
            'orderby' => 'menu_order title',
            'order' => 'ASC'
        ];

        $dm_posts = get_posts($dm_args);

        if (!empty($dm_posts)) {
            $out .= '<div style="margin-bottom:14px;padding:10px 12px;background:#fff8e6;border-left:4px solid #f5a623;border-radius:4px;">';

            foreach ($dm_posts as $dm_post) {
                $summary = get_field('defining_moment_summary', $dm_post->ID);
                // Fall back to truncated title if no summary
                if (empty($summary)) {
                    $summary = wp_trim_words($dm_post->post_title, 8, '...');
                }
                $out .= '<div style="font-size:0.95rem;color:#333;margin-bottom:6px;line-height:1.4;">';
                $out .= '<span style="color:#f5a623;font-weight:bold;">⚡</span> ' . esc_html($summary);
                $out .= '</div>';
            }

            $out .= '</div>';
        }
    }

    if (count($recent_scores) > 0) {
        $out .= '<div style="margin-bottom:12px;font-size:1.03rem;font-weight:500;color:#222;">Previous 5 Days<br>';
        foreach ($recent_scores as $i => $rs) {
            if ($rs < 85) $emo = '🟢';
            elseif ($rs < 110) $emo = '🟡';
            elseif ($rs < 130) $emo = '🟠';
            elseif ($rs < 150) $emo = '🔴';
            else $emo = '🚨';

            $out .= '<span style="font-size:1.15em;font-weight:600;letter-spacing:1px;margin-right:3px;">' . $emo . '</span>';
            $out .= '<span style="font-size:1.02em;font-weight:600;color:#24292f;margin-right:10px;">' . $rs . '</span>';
            if ($i < count($recent_scores) - 1) {
                $out .= '<span style="color:#bbb;font-size:1.1em;margin-right:8px;">|</span>';
            }
        }
        $out .= '</div>';
    }

    $out .= '<div style="margin-top:8px;font-size:0.9rem;color:#555;line-height:1.5;border-top:1px solid #ddd;padding-top:10px;">';
    $out .= '100 = normal day. Volume is headline count. Stories weighted by significance. Defining Moments are the ones history remembers.';
    $out .= '</div>';

    $out .= '</div>';

    return $out;
}
add_shortcode('intensity-weather', 'govbrief_weather_report_shortcode');
