<?php
/**
 * Template Name: GovBrief NJ Internal Social Export
 */

// Get parameters
$selected_date = isset($_GET['date']) ? sanitize_text_field($_GET['date']) : date('Y-m-d');
$issue_number = isset($_GET['issue']) ? intval($_GET['issue']) : 1;

$date_obj = DateTime::createFromFormat('Y-m-d', $selected_date);
$title_date = $date_obj->format('F j, Y');

// Query stories
$args = array(
    'post_type' => 'daily-headlines',
    'posts_per_page' => -1,
    'meta_query' => array(
        'relation' => 'AND',
        array(
            'key' => 'headline_date',
            'value' => date('Ymd', strtotime($selected_date)),
            'compare' => '='
        ),
        array(
            'key' => 'include_in_editions',
            'value' => 's:2:"nj"',
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

?>
<!DOCTYPE html>
<html>
<head>
    <title>NJ Edition - <?php echo $title_date; ?></title>
<style>
    body {
        font-family: Arial, sans-serif;
        max-width: 800px;
        margin: 0 auto;
        padding: 20px 15px;
        background: white;
        line-height: 1.4;
    }
    .date-selector {
        background: #f5f5f5;
        padding: 20px;
        border-radius: 5px;
        margin-bottom: 20px;
        text-align: center;
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
    .header-section {
        text-align: center;
        margin-bottom: 25px;
    }
    .header-logo {
        max-width: 300px;
        height: auto;
        margin-bottom: 15px;
    }
    .nj-edition-badge {
        background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
        color: #78350f;
        display: inline-block;
        padding: 8px 20px;
        border-radius: 20px;
        font-size: 16px;
        font-weight: 700;
        letter-spacing: 0.5px;
        margin-bottom: 10px;
    }
    .issue-number {
        font-size: 15px;
        color: #444;
        margin-top: -5px;
        margin-bottom: 8px;
        text-align: center;
        font-weight: normal;
    }
    h1 {
        font-size: 22px;
        margin: 0 0 5px 0;
        text-align: center;
        font-weight: bold;
    }
    .subscribe-cta {
        text-align: center;
        color: #444;
        margin-bottom: 25px;
        font-size: 14px;
        font-style: italic;
        border-bottom: 1px solid #ddd;
        padding-bottom: 15px;
    }
    .category-section {
        margin-bottom: 20px;
    }
    .category-title {
        background-color: #333;
        color: white;
        font-weight: bold;
        text-transform: uppercase;
        margin-top: 15px;
        margin-bottom: 0;
        padding: 10px 15px;
        font-size: 14px;
        letter-spacing: 0.5px;
        border-radius: 4px;
    }
    .story-list {
        background: #f8f9fa;
        padding: 15px;
        border-left: 4px solid #333;
        margin-bottom: 10px;
    }
    .story-item {
        margin-bottom: 10px;
    }
    .story-source {
        font-style: italic;
        color: #444;
    }
    .divider {
        border-top: 2px solid #333;
        margin: 25px 0 15px 0;
    }
    .links-section {
        background: #f5f5f5;
        padding: 20px;
        border-radius: 5px;
        margin-bottom: 20px;
    }
    .link-item {
        margin-bottom: 5px;
        word-break: break-all;
    }
    .copy-button {
        background: #007cba;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        margin-top: 10px;
        margin-right: 10px;
    }
    .copy-button:hover {
        background: #005a8a;
    }
    @media print {
        .date-selector, .links-section, .copy-button {
            display: none;
        }
    }
</style>
</head>
<body>

<!-- Date Selector -->
<div class="date-selector">
    <form method="get" action="">
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
</div>

<div class="header-section">
    <img src="https://govbrief.today/wp-content/uploads/2025/09/https___substack-post-media.s3.amazonaws.com_public_images_de5c2270-092f-47b4-8d6c-1db9ab80f506_3072x1024.png" alt="GovBrief Logo" class="header-logo">
    <div class="nj-edition-badge">NEW JERSEY EDITION</div>
    <h1>Curated Headlines for <?php echo $title_date; ?></h1>
    <div class="issue-number">Issue #<?php echo $issue_number; ?></div>
</div>

<div class="subscribe-cta">Subscribe free at GovBrief.today for data, trends, images, and search.</div>

<?php
$counter = 1;
$all_links = array();
$stories_by_category = array();

$category_colors = array(
    'Extremism' => '#dc2626', 'Dissent' => '#ea580c', 'Disaster Relief' => '#d97706',
    'Foreign Relations' => '#ca8a04', 'War' => '#65a30d', 'Health' => '#16a34a',
    'Human Rights' => '#059669', 'Environment' => '#0d9488', 'Science' => '#0891b2',
    'DEI' => '#0284c7', 'Voting Rights' => '#2563eb', 'Censorship' => '#4f46e5',
    'Economy' => '#7c3aed', 'Military' => '#9333ea', 'Intelligence' => '#a855f7',
    'Courts' => '#c026d3', 'Criminal Justice' => '#db2777', 'Social Security' => '#e11d48',
    'Immigration' => '#f43f5e', 'Education' => '#f59e0b', 'Oversight' => '#84cc16',
    'Congress' => '#22c55e', 'Federal Personnel' => '#10b981', 'Transportation' => '#14b8a6',
    'Data' => '#06b6d4', 'Propaganda' => '#0ea5e9', 'Religion' => '#3b82f6',
    'Media' => '#6366f1', 'Arts' => '#8b5cf6', 'Grift' => '#a78bfa',
    'Protest' => '#ec4899', 'Fighting Back' => '#f472b6'
);

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

// Display stories by category with colored headers
foreach ($categories as $category) {
    if (!isset($stories_by_category[$category]) || empty($stories_by_category[$category])) {
        continue;
    }
    
    $color = isset($category_colors[$category]) ? $category_colors[$category] : '#6b7280';
    
    echo '<div class="category-section">';
    echo '<div class="category-title" style="background-color: ' . $color . ';">' . strtoupper($category) . '</div>';
    echo '<div class="story-list" style="border-left-color: ' . $color . ';">';
    
    foreach ($stories_by_category[$category] as $story) {
        echo '<div class="story-item">';
        echo $counter . '. ' . esc_html($story['title']);
        
        if ($story['source']) {
            echo ' - <span class="story-source">' . esc_html($story['source']) . '</span>';
        }
        
        echo '</div>';
        
        $all_links[$counter] = $story['link'];
        $counter++;
    }
    
    echo '</div>'; // Close story-list
    echo '</div>'; // Close category-section
}
?>

<div class="divider"></div>

<div class="links-section">
    <h2>Links for First Comment</h2>
    <div id="links-list">
<?php
foreach ($all_links as $num => $link) {
    echo '<div class="link-item">[' . $num . '] ' . esc_url($link) . '</div>';
}
?>
    </div>
    
    <button class="copy-button" onclick="copyLinks()">Copy Links to Clipboard</button>
    <button class="copy-button" onclick="copyStories()">Copy Stories Text</button>
    <button class="copy-button" onclick="window.print()">Print/Save as PDF</button>
</div>

<script>
function setDate(date) {
    window.location.href = '?date=' + date;
}

function copyLinks() {
    let linksText = '';
    <?php foreach ($all_links as $num => $link): ?>
    linksText += '[<?php echo $num; ?>] <?php echo esc_js($link); ?>\n';
    <?php endforeach; ?>
    
    navigator.clipboard.writeText(linksText).then(function() {
        alert('Links copied to clipboard!');
    }, function(err) {
        const textArea = document.createElement('textarea');
        textArea.value = linksText;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        alert('Links copied to clipboard!');
    });
}

function copyStories() {
    const storyElements = document.querySelectorAll('.category-title, .story-item');
    let storiesText = 'GovBrief NEW JERSEY EDITION\n';
    storiesText += 'Curated Headlines for <?php echo $title_date; ?>\n';
    storiesText += 'Issue #<?php echo $issue_number; ?>\n\n';
    storiesText += 'Subscribe free at GovBrief.today for data, trends, images, and search.\n\n';
    
    storyElements.forEach(function(element) {
        storiesText += element.innerText + '\n';
        if (element.className === 'story-item') {
            storiesText += '\n';
        }
    });
    
    navigator.clipboard.writeText(storiesText).then(function() {
        alert('Stories copied to clipboard!');
    }, function(err) {
        const textArea = document.createElement('textarea');
        textArea.value = storiesText;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        alert('Stories copied to clipboard!');
    });
}
</script>

</body>
</html>