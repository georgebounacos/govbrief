<?php
/**
 * Plugin Name: GovBrief Publisher Simple
 * Description: Creates simple lists of headlines and URLs
 * Version: 1.3.3
 * Author: George Bounacos
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class GovBriefPublisherSimple {
    
    public function __construct() {
        // Add meta box to post editor
        add_action('add_meta_boxes', array($this, 'add_format_meta_box'));
        
        // Add required scripts - we'll enqueue directly in the page, not as separate files
        add_action('admin_footer', array($this, 'add_clipboard_script'));
    }
    
    /**
     * Add the meta box to the post editor
     */
    public function add_format_meta_box() {
        add_meta_box(
            'govbrief_formats_simple',
            'GovBrief Headlines and Links',
            array($this, 'render_format_meta_box'),
            'post',
            'normal',
            'high'
        );
    }
    
    /**
     * Add clipboard script directly in the footer
     */
    public function add_clipboard_script() {
        ?>
        <script>
        jQuery(document).ready(function($) {
            $('.govbrief-copy-button').on('click', function() {
                // Get the target textarea ID
                var targetId = $(this).data('target');
                var contentElement = document.getElementById(targetId);
                
                // Select the text
                contentElement.select();
                
                try {
                    // Use the newer clipboard API for better compatibility
                    navigator.clipboard.writeText(contentElement.value).then(function() {
                        // Success - update button text
                        var originalText = $(this).text();
                        $(this).text('Copied!');
                        
                        // Reset button text after 2 seconds
                        setTimeout(function() {
                            $('.govbrief-copy-button[data-target="' + targetId + '"]').text(originalText);
                        }, 2000);
                    }.bind(this));
                } catch (err) {
                    // Fallback to the older method
                    document.execCommand('copy');
                    
                    // Change button text temporarily
                    var originalText = $(this).text();
                    $(this).text('Copied!');
                    
                    // Reset button text after 2 seconds
                    setTimeout(function() {
                        $('.govbrief-copy-button[data-target="' + targetId + '"]').text(originalText);
                    }, 2000);
                }
            });
        });
        </script>
        <style>
        .govbrief-formats-container {
            margin: 20px 0;
        }
        
        .govbrief-format-section {
            margin-bottom: 20px;
            padding: 15px;
            background: #f9f9f9;
            border: 1px solid #e5e5e5;
            border-radius: 4px;
        }
        
        .govbrief-format-section h3 {
            margin-top: 0;
            padding-bottom: 10px;
            border-bottom: 1px solid #e5e5e5;
        }
        
        .govbrief-copy-container {
            text-align: right;
            margin-bottom: 10px;
        }
        
        .govbrief-copy-button {
            background: #2271b1;
            border: none;
            color: white;
            padding: 8px 15px;
            border-radius: 3px;
            cursor: pointer;
            font-size: 14px;
        }
        
        .govbrief-copy-button:hover {
            background: #135e96;
        }
        
        .govbrief-format-content {
            width: 100%;
            height: 300px;
            font-family: monospace;
            padding: 10px;
            font-size: 14px;
        }
        </style>
        <?php
    }
    
    /**
     * Render the meta box content
     */
    public function render_format_meta_box($post) {
        // Get the date from the ACF field if available
        $post_date = get_field('calendar_date', $post->ID);
        
        // If no date in ACF, try to use the post date
        if (empty($post_date)) {
            $post_date = get_the_date('Y-m-d', $post->ID);
        }
        
        // Get headlines and links
        list($headlines, $urls, $sources) = $this->get_headlines_and_links($post_date);
        
        // Format the content
        $headlines_text = $this->format_headlines($headlines);
        $urls_text = $this->format_urls($urls);
        $sources_text = $this->format_sources($sources);
        
        // Output the formatted content
        echo '<div class="govbrief-formats-container">';
        
        // Headlines Section - Modified to show "Headlines" instead of "Numbered Headlines"
        echo '<div class="govbrief-format-section">';
        echo '<h3>Headlines</h3>';
        echo '<div class="govbrief-copy-container">';
        echo '<button class="govbrief-copy-button" data-target="govbrief-headlines">Copy Headlines</button>';
        echo '</div>';
        echo '<textarea id="govbrief-headlines" class="govbrief-format-content" readonly>' . esc_textarea($headlines_text) . '</textarea>';
        echo '</div>';
        
        // URLs Section
        echo '<div class="govbrief-format-section">';
        echo '<h3>Corresponding URLs</h3>';
        echo '<div class="govbrief-copy-container">';
        echo '<button class="govbrief-copy-button" data-target="govbrief-urls">Copy URLs</button>';
        echo '</div>';
        echo '<textarea id="govbrief-urls" class="govbrief-format-content" readonly>' . esc_textarea($urls_text) . '</textarea>';
        echo '</div>';
        
        // Sources Section
        echo '<div class="govbrief-format-section">';
        echo '<h3>Source Names</h3>';
        echo '<div class="govbrief-copy-container">';
        echo '<button class="govbrief-copy-button" data-target="govbrief-sources">Copy Sources</button>';
        echo '</div>';
        echo '<textarea id="govbrief-sources" class="govbrief-format-content" readonly>' . esc_textarea($sources_text) . '</textarea>';
        echo '</div>';
        
        echo '</div>';
    }
    
    /**
     * Get headlines and links for the given date
     */
    public function get_headlines_and_links($date) {
        // Format date for ACF field comparison (ACF stores dates as Ymd, not Y-m-d)
        $formatted_date = date('Ymd', strtotime($date));
        
        // Get regular headlines
        $args = array(
            'post_type' => 'daily-headlines',
            'posts_per_page' => -1,
            'orderby' => 'date',
            'order' => 'ASC',
            'meta_query' => array(
                array(
                    'key' => 'headline_date',
                    'value' => $formatted_date,
                    'compare' => '=',
                    'type' => 'DATE'
                )
            ),
            'category__not_in' => array(get_cat_ID('Fighting Back')),
        );
        
        $headlines_query = new WP_Query($args);
        $headlines = array();
        $urls = array();
        $sources = array();
        
        if ($headlines_query->have_posts()) {
            while ($headlines_query->have_posts()) {
                $headlines_query->the_post();
                $headlines[] = get_the_title();
                $urls[] = get_field('headline_link');
                $sources[] = get_field('headline_source');
            }
            wp_reset_postdata();
        }
        
        // Get Fighting Back headlines (allow multiple)
        $args = array(
            'post_type' => 'daily-headlines',
            'posts_per_page' => -1,
            'orderby' => 'date',
            'order' => 'ASC',
            'meta_query' => array(
                array(
                    'key' => 'headline_date',
                    'value' => $formatted_date,
                    'compare' => '=',
                    'type' => 'DATE'
                )
            ),
            'category_name' => 'fighting-back',
        );
        
        $fighting_back_query = new WP_Query($args);

        if ($fighting_back_query->have_posts()) {
            while ($fighting_back_query->have_posts()) {
                $fighting_back_query->the_post();
                $headlines[] = "FIGHTING BACK: " . get_the_title();
                $urls[] = get_field('headline_link');
                $sources[] = get_field('headline_source');
            }
            wp_reset_postdata();
        }
        
        return array($headlines, $urls, $sources);
    }
    
    /**
     * Format headlines without numbering
     * Modified to remove the counter
     */
    public function format_headlines($headlines) {
        $formatted = "";
        
        foreach ($headlines as $headline) {
            // Decode HTML entities in the headline
            $decoded_headline = html_entity_decode($headline, ENT_QUOTES);
            
            // Add headline without numbering
            $formatted .= $decoded_headline . "\n";
        }
        
        return $formatted;
    }
    
    /**
     * Format URLs as a numbered list
     */
    public function format_urls($urls) {
        $formatted = "";
        $counter = 1;
        
        foreach ($urls as $index => $url) {
            // Check if this corresponds to Fighting Back headline
            if ($index > 0 && isset($headlines[$index]) && strpos($headlines[$index], 'FIGHTING BACK:') === 0) {
                $formatted .= "FIGHTING BACK: " . ($url ? $url : "No URL") . "\n\n";
            } else {
                $formatted .= $counter . ". " . ($url ? $url : "No URL") . "\n\n";
                $counter++;
            }
        }
        
        return $formatted;
    }
    
    // This method is kept for backward compatibility but no longer used in the interface
    public function format_hyperlinked_headlines($headlines, $urls) {
        $formatted = "";
        $counter = 1;
        
        foreach ($headlines as $index => $headline) {
            // Decode HTML entities in the headline
            $decoded_headline = html_entity_decode($headline, ENT_QUOTES);
            $url = isset($urls[$index]) ? $urls[$index] : '';
            
            // Special case for Fighting Back
            if (strpos($decoded_headline, 'FIGHTING BACK:') === 0) {
                // Extract the actual headline after "FIGHTING BACK: "
                $actual_headline = substr($decoded_headline, 14); // 14 is the length of "FIGHTING BACK: "
                
                // Get first two words
                $words = preg_split('/\s+/', trim($actual_headline), 3);
                $first_two_words = isset($words[1]) ? $words[0] . ' ' . $words[1] : $words[0];
                $remaining_text = isset($words[2]) ? ' ' . $words[2] : '';
                
                // Format with hyperlink
                if ($url) {
                    $formatted .= "\nFIGHTING BACK: <a href=\"" . esc_url($url) . "\">" . esc_html($first_two_words) . "</a>" . esc_html($remaining_text) . "\n\n";
                } else {
                    $formatted .= "\nFIGHTING BACK: " . esc_html($actual_headline) . "\n\n";
                }
            } else {
                // Regular headline - get first two words
                $words = preg_split('/\s+/', trim($decoded_headline), 3);
                $first_two_words = isset($words[1]) ? $words[0] . ' ' . $words[1] : $words[0];
                $remaining_text = isset($words[2]) ? ' ' . $words[2] : '';
                
                // Format with number and hyperlink
                if ($url) {
                    $formatted .= $counter . ". <a href=\"" . esc_url($url) . "\">" . esc_html($first_two_words) . "</a>" . esc_html($remaining_text) . "\n\n";
                } else {
                    $formatted .= $counter . ". " . esc_html($decoded_headline) . "\n\n";
                }
                
                $counter++;
            }
        }
        
        return $formatted;
    }
    
    /**
     * Format sources as a comma-separated list for human readability
     * With duplicate removal
     */
    public function format_sources($sources) {
        // Filter out empty sources
        $filtered_sources = array_filter($sources, function($source) {
            return !empty($source);
        });
        
        // If no sources, return message
        if (empty($filtered_sources)) {
            return "No sources found.";
        }
        
        // Remove duplicates
        $unique_sources = array_unique($filtered_sources);
        
        // Create comma-separated list with space after each comma
        $formatted = implode(', ', $unique_sources);
        
        return $formatted;
    }
}

// Initialize the plugin
new GovBriefPublisherSimple();