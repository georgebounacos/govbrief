<?php
// Add this to your WP admin
// Visit: yoursite.com/wp-admin/admin.php?page=clear-govbrief-cache

add_action('admin_menu', function() {
    add_menu_page(
        'Clear GovBrief Cache',
        'Clear Cache',
        'manage_options',
        'clear-govbrief-cache',
        'govbrief_clear_cache_page',
        'dashicons-update'
    );
});

function govbrief_clear_cache_page() {
    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized');
    }
    
    $cleared = array();
    
    // If form submitted
    if (isset($_POST['clear_cache']) && check_admin_referer('clear_govbrief_cache')) {
        $date = sanitize_text_field($_POST['cache_date']);
        
        // Clear cards cache for this date
        $cache_key = 'govbrief_cards_' . md5($date);
        $result = delete_transient($cache_key);
        $cleared[] = "Cards cache for $date: " . ($result ? 'CLEARED' : 'not found');
        
        // Clear trending cache
        $trending_key = 'govbrief_trending_' . md5($date);
        $result2 = delete_transient($trending_key);
        $cleared[] = "Trending cache for $date: " . ($result2 ? 'CLEARED' : 'not found');
        
        // Clear homepage cache
        delete_transient('govbrief_homepage_cards_6');
        $cleared[] = "Homepage cache: CLEARED";
    }
    
    ?>
    <div class="wrap">
        <h1>Clear GovBrief Cache</h1>
        
        <?php if (!empty($cleared)): ?>
            <div class="notice notice-success">
                <ul>
                    <?php foreach($cleared as $msg): ?>
                        <li><?php echo esc_html($msg); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <form method="post" action="">
            <?php wp_nonce_field('clear_govbrief_cache'); ?>
            
            <table class="form-table">
                <tr>
                    <th><label for="cache_date">Date (YYYY-MM-DD)</label></th>
                    <td>
                        <input type="date" name="cache_date" id="cache_date" value="<?php echo date('Y-m-d'); ?>" required>
                        <p class="description">Enter the date for which you want to clear the cache</p>
                    </td>
                </tr>
            </table>
            
            <p class="submit">
                <input type="submit" name="clear_cache" class="button button-primary" value="Clear Cache">
            </p>
        </form>
        
        <hr>
        
        <h2>Quick Links</h2>
        <ul>
            <li><a href="<?php echo admin_url('admin.php?page=clear-govbrief-cache'); ?>" class="button">Clear Today's Cache</a></li>
        </ul>
    </div>
    <?php
}
