<?php
/**
 * GovBrief Admin Tools
 *
 * Admin-only tools: Heather's Headlines search/export, cache clearing.
 */

if (!defined('ABSPATH')) exit;

// ========================================
// Heather's Headlines: Search & Export Tool
// ========================================

// REST endpoint for tag autocomplete: /wp-json/gbt/v1/tags?q=mai
add_action('rest_api_init', function(){
    register_rest_route('gbt/v1', '/tags', [
        'methods'  => 'GET',
        'callback' => function(\WP_REST_Request $req){
            $q = sanitize_text_field($req->get_param('q'));
            $args = [
                'taxonomy'   => 'post_tag',
                'hide_empty' => false,
                'number'     => 20,
                'orderby'    => 'count',
                'order'      => 'DESC',
            ];
            if ($q !== '') {
                $args['name__like'] = $q;
            }
            $terms = get_terms($args);
            $out = [];
            if (!is_wp_error($terms)) {
                foreach ($terms as $t) {
                    $out[] = ['id' => $t->term_id, 'text' => $t->name];
                }
            }
            return rest_ensure_response($out);
        },
        'permission_callback' => function(){ return current_user_can('edit_posts'); }
    ]);
});

// Shortcode: [gbt_search]
add_shortcode('gbt_search', function(){
    if (!current_user_can('edit_posts')) {
        return '<p>Access restricted.</p>';
    }

    // read inputs
    $kw    = isset($_GET['kw']) ? sanitize_text_field($_GET['kw']) : '';
    $tags  = isset($_GET['tags']) ? array_filter(array_map('intval', (array) $_GET['tags'])) : [];
    $paged = max(1, isset($_GET['pg']) ? intval($_GET['pg']) : 1);
    $export = isset($_GET['gbt_export']) && $_GET['gbt_export'] === '1';

    // build WP_Query args
    $args = [
        'post_type'      => 'daily-headlines',
        'post_status'    => 'publish',
        'posts_per_page' => 25,
        'paged'          => $paged,
        'orderby'        => 'date',
        'order'          => 'DESC',
    ];
    if ($kw !== '') {
        $args['s'] = $kw;
        $args['search_columns'] = ['post_title'];
    }
    if (!empty($tags)) {
        $tax_query = [];
        foreach ($tags as $tid) {
            $tax_query[] = [
                'taxonomy' => 'post_tag',
                'field'    => 'term_id',
                'terms'    => [$tid],
                'operator' => 'IN'
            ];
        }
        if (count($tax_query) > 1) {
            $tax_query['relation'] = 'AND';
        }
        $args['tax_query'] = $tax_query;
    }

    // export CSV if requested
    if ($export) {
        $all_args = $args;
        $all_args['posts_per_page'] = -1;
        $all_args['paged'] = 1;
        $q = new WP_Query($all_args);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=govbrief-results.csv');
        $out = fopen('php://output', 'w');
        fputcsv($out, ['ID','Date','Title','Categories','Tags','Permalink']);
        while ($q->have_posts()) { $q->the_post();
            $id   = get_the_ID();
            $cats = wp_get_post_terms($id, 'category', ['fields'=>'names']);
            $tg   = wp_get_post_terms($id, 'post_tag',  ['fields'=>'names']);
            fputcsv($out, [
                $id,
                get_the_date('Y-m-d', $id),
                html_entity_decode( get_the_title($id), ENT_QUOTES ),
                implode(', ', $cats),
                implode(', ', $tg),
                get_permalink($id),
            ]);
        }
        wp_reset_postdata();
        fclose($out);
        exit;
    }

    // run query for on-page results
    $q = new WP_Query($args);
    $base = esc_url(remove_query_arg(['pg','gbt_export']));

    ob_start(); ?>
    <div class="gbt-wrap">
        <form method="get" action="<?php echo $base; ?>" class="gbt-form" style="margin-bottom:12px;">
            <label style="display:block;margin-bottom:6px;">
                Headline words
                <input type="search" name="kw" value="<?php echo esc_attr($kw); ?>" placeholder="type words in the headline" style="width:100%;max-width:480px;">
            </label>

            <label style="display:block;margin-bottom:6px;">
                Tags
                <select id="gbt-tagpicker" name="tags[]" multiple="multiple" style="width:100%;max-width:640px;"></select>
            </label>

            <button type="submit">Search</button>
            <?php if ($q->have_posts()) : ?>
                <a href="<?php echo esc_url(add_query_arg('gbt_export','1',$base . (strpos($base,'?')!==false ? '' : '?' ) . http_build_query(['kw'=>$kw,'tags'=>$tags]))); ?>" class="button" style="margin-left:8px;">Download CSV</a>
            <?php endif; ?>
        </form>

        <div class="gbt-results">
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th style="width:80px;">Date</th>
                        <th>Title</th>
                        <th style="width:22%;">Categories</th>
                        <th style="width:22%;">Tags</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($q->have_posts()) : while ($q->have_posts()) : $q->the_post();
                    $id   = get_the_ID();
                    $cats = wp_get_post_terms($id, 'category', ['fields'=>'names']);
                    $tg   = wp_get_post_terms($id, 'post_tag',  ['fields'=>'names']); ?>
                    <tr>
                        <td><?php echo esc_html( get_the_date('Y-m-d') ); ?></td>
                        <td><a href="<?php the_permalink(); ?>" target="_blank" rel="noopener"><?php the_title(); ?></a></td>
                        <td><?php echo esc_html(implode(', ', $cats)); ?></td>
                        <td><?php echo esc_html(implode(', ', $tg)); ?></td>
                    </tr>
                <?php endwhile; else: ?>
                    <tr><td colspan="4">No results.</td></tr>
                <?php endif; wp_reset_postdata(); ?>
                </tbody>
            </table>

            <?php
            if ($q->max_num_pages > 1) {
                $cur = max(1, $paged);
                echo '<p style="margin-top:10px;">';
                if ($cur > 1) {
                    $prev_url = esc_url(add_query_arg('pg', $cur - 1, $base . (strpos($base,'?')!==false ? '' : '?' ) . http_build_query(['kw'=>$kw,'tags'=>$tags])));
                    echo '<a href="'.$prev_url.'">« Prev</a> ';
                }
                echo 'Page '.$cur.' of '.$q->max_num_pages;
                if ($cur < $q->max_num_pages) {
                    $next_url = esc_url(add_query_arg('pg', $cur + 1, $base . (strpos($base,'?')!==false ? '' : '?' ) . http_build_query(['kw'=>$kw,'tags'=>$tags])));
                    echo ' <a href="'.$next_url.'">Next »</a>';
                }
                echo '</p>';
            }
            ?>
        </div>
    </div>
    <?php
    add_action('wp_footer', function() use ($tags){
        ?>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.full.min.js"></script>
        <script>
            (function(){
                var preselected = <?php echo wp_json_encode(array_values($tags)); ?>;
                function initSelect(){
                    var $el = jQuery('#gbt-tagpicker');
                    if (!$el.length) return;

                    $el.select2({
                        ajax: {
                            url: '<?php echo esc_url( rest_url('gbt/v1/tags') ); ?>',
                            dataType: 'json',
                            delay: 250,
                            data: function (params) { return { q: params.term || '' }; },
                            processResults: function (data) { return { results: data }; },
                            cache: true
                        },
                        placeholder: 'type to find tags',
                        minimumInputLength: 2,
                        width: 'resolve'
                    });

                    if (preselected && preselected.length){
                        jQuery.ajax({
                            url: '<?php echo esc_url( rest_url('gbt/v1/tags') ); ?>',
                            data: { q: '' },
                            success: function(data){
                                var map = {};
                                data.forEach(function(it){ map[it.id] = it.text; });
                                preselected.forEach(function(id){
                                    var opt = new Option(map[id] || ('#'+id), id, true, true);
                                    $el.append(opt);
                                });
                                $el.trigger('change');
                            }
                        });
                    }
                }
                if (window.jQuery) initSelect(); else document.addEventListener('DOMContentLoaded', initSelect);
            })();
        </script>
        <?php
    });
    return ob_get_clean();
});


// ========================================
// GovBrief Cache Clearing Tool
// ========================================
// Usage: Add ?clear_gb_cache=1 to any URL while logged in as admin
// Example: https://govbrief.today/?clear_gb_cache=1

add_action('init', function() {
    if (!isset($_GET['clear_gb_cache']) || $_GET['clear_gb_cache'] !== '1') {
        return;
    }

    if (!current_user_can('manage_options')) {
        return;
    }

    global $wpdb;

    // Delete all govbrief-related transients
    $deleted = $wpdb->query(
        "DELETE FROM {$wpdb->options}
         WHERE option_name LIKE '_transient_govbrief_%'
         OR option_name LIKE '_transient_timeout_govbrief_%'"
    );

    // Also clear the homepage cards cache explicitly
    delete_transient('govbrief_homepage_cards_6');

    // Admin notice
    add_action('admin_notices', function() use ($deleted) {
        echo '<div class="notice notice-success is-dismissible">';
        echo '<p><strong>GovBrief cache cleared.</strong> ' . intval($deleted) . ' transient entries removed.</p>';
        echo '</div>';
    });

    // Frontend notice for non-admin pages
    if (!is_admin()) {
        add_action('wp_footer', function() use ($deleted) {
            echo '<div style="position:fixed;bottom:20px;right:20px;background:#10b981;color:white;padding:15px 20px;border-radius:8px;font-family:sans-serif;font-weight:600;z-index:99999;box-shadow:0 4px 12px rgba(0,0,0,0.2);">';
            echo '✓ GovBrief cache cleared (' . intval($deleted) . ' entries)';
            echo '</div>';
        });
    }
});
