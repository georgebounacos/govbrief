<?php
/**
 * Template Name: GovBrief NJ Internal Substack Export
 */

// Get parameters
$selected_date = isset($_GET['date']) ? sanitize_text_field($_GET['date']) : date('Y-m-d');
$issue_number = isset($_GET['issue']) ? intval($_GET['issue']) : 1;
$format = isset($_GET['format']) ? sanitize_text_field($_GET['format']) : 'plain'; // 'plain' or 'cards'

$date_obj = DateTime::createFromFormat('Y-m-d', $selected_date);
$title_date = $date_obj->format('F j, Y');

// Query stories
$date_formats = array(
    $selected_date,
    date('Ymd', strtotime($selected_date)),
    date('m/d/Y', strtotime($selected_date)),
);

$args = array(
    'post_type' => 'daily-headlines',
    'posts_per_page' => -1,
    'meta_query' => array(
        'relation' => 'AND',
        array(
            'key' => 'headline_date',
            'value' => $date_formats,
            'compare' => 'IN'
        ),
        array(
            'key' => 'include_in_editions',
            'value' => 'nj',
            'compare' => 'LIKE'
        )
    ),
    'orderby' => 'menu_order',
    'order' => 'ASC'
);

$headlines = new WP_Query($args);

$categories = array(
    'Extremism', 'Dissent', 'Disaster Relief', 'Foreign Relations', 'War', 'Health',
    'Human Rights', 'Environment', 'Science', 'DEI', 'Voting Rights', 'Censorship',
    'Economy', 'Military', 'Intelligence', 'Courts', 'Criminal Justice', 'Social Security',
    'Immigration', 'Education', 'Oversight', 'Congress', 'Federal Personnel', 'Transportation',
    'Data', 'Propaganda', 'Religion', 'Media', 'Arts', 'Grift', 'Protest', 'Fighting Back'
);

// Category colors for card format
$category_colors = array(
    'Extremism' => '#dc2626',
    'Dissent' => '#ea580c',
    'Disaster Relief' => '#f59e0b',
    'Foreign Relations' => '#2563eb',
    'War' => '#7c2d12',
    'Health' => '#16a34a',
    'Human Rights' => '#db2777',
    'Environment' => '#059669',
    'Science' => '#0891b2',
    'DEI' => '#7c3aed',
    'Voting Rights' => '#4f46e5',
    'Censorship' => '#dc2626',
    'Economy' => '#16a34a',
    'Military' => '#475569',
    'Intelligence' => '#1e293b',
    'Courts' => '#7c3aed',
    'Criminal Justice' => '#be123c',
    'Social Security' => '#0d9488',
    'Immigration' => '#ea580c',
    'Education' => '#2563eb',
    'Oversight' => '#64748b',
    'Congress' => '#1e40af',
    'Federal Personnel' => '#6366f1',
    'Transportation' => '#0891b2',
    'Data' => '#6b7280',
    'Propaganda' => '#dc2626',
    'Religion' => '#7c3aed',
    'Media' => '#059669',
    'Arts' => '#db2777',
    'Grift' => '#b91c1c',
    'Protest' => '#ea580c',
    'Fighting Back' => '#16a34a'
);

?>
<!DOCTYPE html>
<html>
<head>
    <title>NJ Substack - <?php echo $title_date; ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background: white;
        }
        .date-selector {
            background: #f5f5f5;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .date-selector input[type="date"] {
            padding: 8px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin: 0 10px;
        }
        .date-selector button {
            background: #007cba;
            color: white;
            padding: 8px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        .date-selector button:hover {
            background: #005a8a;
        }
        .quick-dates {
            margin-top: 10px;
        }
        .quick-dates button {
            background: #666;
            color: white;
            padding: 5px 15px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            margin: 0 5px;
            font-size: 14px;
        }
        .quick-dates button:hover {
            background: #444;
        }
        .format-toggle {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
        }
        .format-toggle label {
            margin-right: 20px;
            cursor: pointer;
        }
        .controls {
            background: #e8f4f8;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .copy-button {
            background: #007cba;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .copy-button:hover {
            background: #005a8a;
        }
        #substack-content {
            background: white;
            padding: 15px;
            border: 1px solid #ddd;
            font-family: Arial, sans-serif;
            line-height: 1.6;
        }
        #substack-content p {
            margin: 10px 0;
        }
        #substack-content a {
            color: #007cba;
            text-decoration: underline;
        }
        /* Card format styles */
        .story-card {
            border-left: 4px solid #2563eb;
            background: #f8f9fa;
            padding: 15px;
            margin-bottom: 12px;
        }
        .story-card a {
            text-decoration: none;
            color: #1a1a1a;
            font-weight: 600;
        }
        .story-card a:hover {
            color: #0066cc;
        }
        .category-label {
            font-size: 11px;
            font-weight: bold;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }
        .story-source {
            font-size: 13px;
            color: #666;
            font-style: italic;
            margin-top: 5px;
        }
    </style>
</head>
<body>

<!-- Date Selector -->
<div class="date-selector">
    <form method="get" action="">
        <input type="hidden" name="format" value="<?php echo esc_attr($format); ?>">
        <label for="date-picker">Select Date:</label>
        <input type="date" id="date-picker" name="date" value="<?php echo $selected_date; ?>" max="<?php echo date('Y-m-d'); ?>">
        <button type="submit">Load Date</button>
    </form>
    
    <div class="quick-dates">
        Quick select: 
        <button onclick="setDate('<?php echo date('Y-m-d'); ?>')">Today</button>
        <button onclick="setDate('<?php echo date('Y-m-d', strtotime('-1 day')); ?>')">Yesterday</button>
        <button onclick="setDate('<?php echo date('Y-m-d', strtotime('-2 days')); ?>')">2 Days Ago</button>
        <button onclick="setDate('<?php echo date('Y-m-d', strtotime('-3 days')); ?>')">3 Days Ago</button>
        <button onclick="setDate('<?php echo date('Y-m-d', strtotime('-7 days')); ?>')">1 Week Ago</button>
    </div>
    
    <div class="format-toggle">
        <strong>Format:</strong>
        <label>
            <input type="radio" name="format-choice" value="plain" <?php checked($format, 'plain'); ?> onchange="changeFormat('plain')">
            Plain Text (current)
        </label>
        <label>
            <input type="radio" name="format-choice" value="cards" <?php checked($format, 'cards'); ?> onchange="changeFormat('cards')">
            Card Format (visual)
        </label>
    </div>
</div>

<div class="controls">
    <h2>For Substack Email (Copy & Paste)</h2>
    <p>Stories from <strong><?php echo $title_date; ?></strong></p>
    <p>Click the button below to copy the formatted HTML. Then paste directly into the Substack editor.</p>
    <button class="copy-button" onclick="copySubstackContent()">Copy for Substack</button>
</div>

<div id="substack-content">
<?php
$story_counter = 1;
$stories_by_category = array();

if ($headlines->have_posts()) {
    while ($headlines->have_posts()) {
        $headlines->the_post();
        
        $primary_cat_id = get_post_meta(get_the_ID(), '_yoast_wpseo_primary_category', true);
        
        if ($primary_cat_id) {
            $primary_cat = get_category($primary_cat_id);
            $category_name = $primary_cat ? $primary_cat->name : 'Uncategorized';
        } else {
            $post_categories = wp_get_post_categories(get_the_ID());
            if (!empty($post_categories)) {
                $first_cat = get_category($post_categories[0]);
                $category_name = $first_cat ? $first_cat->name : 'Uncategorized';
            } else {
                $category_name = 'Uncategorized';
            }
        }
        
        if (!isset($stories_by_category[$category_name])) {
            $stories_by_category[$category_name] = array();
        }
        
        $stories_by_category[$category_name][] = array(
            'title' => get_the_title(),
            'link' => get_field('headline_link') ?: get_permalink(),
            'source' => get_field('headline_source')
        );
    }
    wp_reset_postdata();
}

// Display stories by category
if ($format === 'cards') {
    // CARD FORMAT - Each story in a visual card
    foreach ($categories as $category) {
        if (!isset($stories_by_category[$category]) || empty($stories_by_category[$category])) {
            continue;
        }
        
        foreach ($stories_by_category[$category] as $story) {
            $title = $story['title'];
            $link = $story['link'];
            $source = $story['source'];
            $color = isset($category_colors[$category]) ? $category_colors[$category] : '#6b7280';
            
            // Add UTM parameters
            $link = add_query_arg(array(
                'utm_source'   => 'substack',
                'utm_medium'   => 'email',
                'utm_campaign' => 'nj_edition'
            ), $link);
            
            echo '<div class="story-card" style="border-left-color: ' . esc_attr($color) . ';">';
            echo '<div class="category-label">' . strtoupper(esc_html($category)) . '</div>';
            echo '<a href="' . esc_url($link) . '">';
            echo '<strong>' . $story_counter . '. ' . esc_html($title) . '</strong>';
            echo '</a>';
            if ($source) {
                echo '<div class="story-source">' . esc_html($source) . '</div>';
            }
            echo '</div>';
            
            $story_counter++;
        }
    }
} else {
    // PLAIN TEXT FORMAT - First three words linked (your current format)
    foreach ($categories as $category) {
        if (!isset($stories_by_category[$category]) || empty($stories_by_category[$category])) {
            continue;
        }
        
        echo '<p><strong>' . strtoupper($category) . '</strong></p>';
        
        foreach ($stories_by_category[$category] as $story) {
            $title = $story['title'];
            $link = $story['link'];
            $source = $story['source'];
            
            // Add UTM parameters
            $link = add_query_arg(array(
                'utm_source'   => 'substack',
                'utm_medium'   => 'email',
                'utm_campaign' => 'nj_edition'
            ), $link);
            
            // Split title into words
            $words = explode(' ', $title);
            $first_three = array_slice($words, 0, 3);
            $rest = array_slice($words, 3);
            
            echo '<p>' . $story_counter . '. ';
            echo '<a href="' . esc_url($link) . '">' . esc_html(implode(' ', $first_three)) . '</a>';
            if (count($rest) > 0) {
                echo ' ' . esc_html(implode(' ', $rest));
            }
            
            if ($source) {
                echo ' - <em>' . esc_html($source) . '</em>';
            }
            
            echo '</p>';
            $story_counter++;
        }
    }
}

if ($story_counter === 1) {
    echo '<p style="color: #999; padding: 20px; text-align: center;">No stories found for ' . $title_date . ' in the NJ edition.</p>';
}
?>
</div>

<script>
function setDate(date) {
    const currentFormat = '<?php echo esc_js($format); ?>';
    window.location.href = '?date=' + date + '&format=' + currentFormat;
}

function changeFormat(format) {
    const currentDate = '<?php echo esc_js($selected_date); ?>';
    window.location.href = '?date=' + currentDate + '&format=' + format;
}

function copySubstackContent() {
    const content = document.getElementById('substack-content');
    const range = document.createRange();
    range.selectNode(content);
    window.getSelection().removeAllRanges();
    window.getSelection().addRange(range);
    
    try {
        const successful = document.execCommand('copy');
        if(successful) {
            alert('Content copied! Paste directly into Substack editor and the formatting will be preserved.');
        } else {
            alert('Copy failed. Please try selecting the content manually.');
        }
    } catch(err) {
        alert('Copy failed. Please try selecting the content manually.');
    }
    
    window.getSelection().removeAllRanges();
}
</script>

</body>
</html>