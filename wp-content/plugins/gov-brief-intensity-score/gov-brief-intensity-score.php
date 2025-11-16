<?php
/**
 * Plugin Name: GovBrief Intensity Score
 * Plugin URI: https://govbrief.today
 * Description: Displays the GovBrief Intensity Score and Recent Intensity Score with a 5-day history.
 * Version: 1.5
 * Author: George Bounacos
 * License: GPL2
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! function_exists('get_intensity_emoji') ) {
    function get_intensity_emoji($score) {
        if ( $score < 85 ) {
            return '🟢';
        } elseif ( $score < 110 ) {
            return '🟡';
        } elseif ( $score < 130 ) {
            return '🟠';
        } elseif ( $score < 150 ) {
            return '🔴';
        } else {
            return '🔥';
        }
    }
}

function govbrief_intensity_score_shortcode( $atts ) {
    global $post;

    $calendar_date_raw = get_field('calendar_date', $post->ID) ?: get_the_date('Y-m-d', $post->ID);
    $calendar_date_obj = DateTime::createFromFormat('Y-m-d', $calendar_date_raw) ?: new DateTime();

    $intensity_score = intval( get_field('intensity_score', $post->ID) ?: 100 );
    $recent_intensity_score = intval( get_field('recent_intensity_score', $post->ID) ?: 100 );

    $intensity_emoji = get_intensity_emoji($intensity_score);
    $recent_intensity_emoji = get_intensity_emoji($recent_intensity_score);

    ob_start();
    ?>
    <div style="border:4px solid #3a3a3a; padding:12px; background:#f1f1f1; max-width:800px; margin:auto;">
        <div style="font-weight:bold; font-size:1.1em; margin-bottom:8px;">
            <?php echo esc_html( $calendar_date_obj->format('F j, Y') ); ?>
        </div>
        <div style="font-weight:bold; font-size:1.3em; margin-bottom:4px;">
            GovBrief Intensity Score (All Time): <?php echo intval($intensity_score); ?> <?php echo $intensity_emoji; ?>
        </div>
        <?php
        $hist = [];
        $pt = get_post_type($post->ID);

        for ( $i = 1; $i <= 5; $i++ ) {
            $d = clone $calendar_date_obj;
            $d->modify("-{$i} days");
            $db_target = $d->format('Ymd');

            $q = new WP_Query([
                'post_type'      => $pt,
                'posts_per_page' => 1,
                'fields'         => 'ids',
                'meta_query'     => [[
                    'key'     => 'calendar_date',
                    'value'   => $db_target,
                    'compare' => '=',
                ]],
            ]);

            if ( $q->have_posts() ) {
                $pid = $q->posts[0];
                $hs = get_field('intensity_score', $pid);
                $val = $hs !== '' ? intval($hs) : '-';
            } else {
                $val = '-';
            }
            wp_reset_postdata();

            $emoji = ($val !== '-') ? get_intensity_emoji($val) : '';

            $hist[] = '<span style="margin:0 4px;font-weight:bold;">'
                    . esc_html($val) . ' ' . esc_html($emoji)
                    . '</span>';
        }
        ?>
        <div style="margin-top:8px; font-weight:bold;">
            Previous 5 Days: <?php echo implode('|', $hist); ?>
        </div>
        <div style="font-weight:bold; font-size:1.3em; margin-top:12px;">
            Recent Intensity (vs. 60 Days): <?php echo intval($recent_intensity_score); ?> <?php echo $recent_intensity_emoji; ?>
        </div>
    </div>
    <?php

    return ob_get_clean();
}

remove_shortcode('intensity-score');
add_shortcode('govbrief-intensity-score', 'govbrief_intensity_score_shortcode');
