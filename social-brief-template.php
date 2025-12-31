<?php
/*
Template Name: Social Brief Generator
*/

// Password protection check 
if ( post_password_required() ) {
    get_header();
    echo '<div style="max-width: 800px; margin: 40px auto; padding: 20px;">';
    echo '<h1>Protected Content</h1>';
    echo get_the_password_form();
    echo '</div>';
    get_footer();
    exit;
}

// Get date from URL parameter or default to today
$selected_date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

// Format for display: 250829 style
$date_obj = DateTime::createFromFormat('Y-m-d', $selected_date);
$display_date = $date_obj->format('ymd');

// Format for the title: September 1, 2025
$title_date = $date_obj->format('F j, Y');

// Calculate issue number
// If August 30, 2025 was #212, we can calculate from there
$base_date = DateTime::createFromFormat('Y-m-d', '2025-08-30');
$base_issue_number = 212;

// Calculate days difference
$interval = $base_date->diff($date_obj);
$days_diff = $interval->days;

// Account for whether we're before or after the base date
if ($date_obj < $base_date) {
    $issue_number = $base_issue_number - $days_diff;
} else {
    $issue_number = $base_issue_number + $days_diff;
}

// Your categories in priority order
$categories = [
    'Extremism',
    'Dissent',
    'Disaster Relief',
    'Foreign Relations',
    'War',
    'Health',
    'Human Rights',
    'Environment',
    'Science',
    'DEI',
    'Voting Rights',
    'Censorship',
    'Economy',
    'Military',
    'Intelligence',
    'Justice Dept',
    'Courts',
    'Criminal Justice',
    'Social Security',
    'Immigration',
    'Education',
    'Oversight',
    'Congress',
    'Federal Personnel',
    'Transportation',
    'Data',
    'Propaganda',
    'Religion',
    'Media',
    'Arts',
    'Grift',
    'Protest',
    'Fighting Back'
];

// Category color mapping for full-width bars
$category_colors = [
    'Extremism' => '#dc2626',
    'Dissent' => '#ea580c',
    'Disaster Relief' => '#ca8a04',
    'Foreign Relations' => '#65a30d',
    'War' => '#16a34a',
    'Health' => '#059669',
    'Human Rights' => '#0d9488',
    'Environment' => '#0891b2',
    'Science' => '#0284c7',
    'DEI' => '#2563eb',
    'Voting Rights' => '#4f46e5',
    'Censorship' => '#7c3aed',
    'Economy' => '#9333ea',
    'Military' => '#a21caf',
    'Intelligence' => '#c026d3',
    'Justice Dept' => '#db2777',
    'Courts' => '#e11d48',
    'Criminal Justice' => '#f43f5e',
    'Social Security' => '#fb923c',
    'Immigration' => '#fbbf24',
    'Education' => '#facc15',
    'Oversight' => '#a3e635',
    'Congress' => '#4ade80',
    'Federal Personnel' => '#34d399',
    'Transportation' => '#2dd4bf',
    'Data' => '#22d3ee',
    'Propaganda' => '#38bdf8',
    'Religion' => '#60a5fa',
    'Media' => '#818cf8',
    'Arts' => '#a78bfa',
    'Grift' => '#c084fc',
    'Protest' => '#e879f9',
    'Fighting Back' => '#f472b6'
];

?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $display_date; ?> - Happened Today</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px 15px;
            background: white;
            line-height: 1.4;
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
        .issue-number {
            font-size: 15px;
            color: #444;
            margin-top: -5px;
            margin-bottom: 8px;
            text-align: center;
            font-weight: normal;
        }
        .date-selector {
            background: #f5f5f5;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 30px;
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
            margin-bottom: 15px;
        }
        .category-title {
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 15px;
            margin-bottom: 8px;
            font-size: 14px;
            letter-spacing: 0.5px;
            color: #1a1a1a;
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
        @media print {
            .date-selector, .links-section, .copy-button {
                display: none;
            }
        }
        /* Additional styles for enhanced social media format */
        .category-title {
            background: #333;
            color: white;
            padding: 8px 15px;
            margin: 20px -30px 12px -30px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 13px;
            letter-spacing: 0.8px;
        }
        
        .story-item {
            color: #1a1a1a;
            line-height: 1.5;
        }
        
        /* Orange numbers */
        .story-number {
            color: #ea580c;
            font-weight: bold;
        }
        
        /* Gray inline sources */
        .inline-source {
            color: #666;
            font-weight: normal;
        }
        
        /* Content area for image export */
        #export-content {
            background: white;
            padding: 30px;
            padding-bottom: 80px;
            width: 550px;
            margin: 0 auto;
        }
        
        /* Hide date selector and buttons when exporting */
        @media print {
            .date-selector,
            .links-section,
            .copy-button,
            .export-buttons {
                display: none !important;
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
        <button type="submit">Generate Brief</button>
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

<?php
// Query for the daily post by calendar_date to render shortcodes
$daily_post_query = new WP_Query(array(
    'post_type' => 'post',
    'posts_per_page' => 1,
    'post_status' => 'publish',
    'meta_query' => array(
        array(
            'key' => 'calendar_date',
            'value' => $selected_date,
            'compare' => '=',
            'type' => 'DATE'
        )
    ),
    'orderby' => 'date',
    'order' => 'DESC'
));

$daily_post_found = false;
$found_post_id = null;
$found_post_title = '';

if ($daily_post_query->have_posts()) {
    $daily_post_found = true;
    $queried_post = $daily_post_query->posts[0];
    $found_post_id = $queried_post->ID;
    $found_post_title = $queried_post->post_title;
}
?>

<?php if ($daily_post_found): ?>
<!-- Substack Metrics Section -->
<div class="links-section" style="margin-bottom: 30px;">
    <h2>For Substack Email - Metrics Images</h2>
    <p>Download images to paste into Substack. The gaps between boxes will be transparent and blend with your email background.</p>
    <p style="font-size: 13px; color: #666;">Using post: <strong><?php echo $found_post_title; ?></strong> (ID: <?php echo $found_post_id; ?>)</p>
    
    <!-- Block 1: Intensity + Trending + Quote -->
    <div id="metrics-block-1" style="background: transparent; padding: 0; width: 600px;">
        <div style="margin-bottom: 20px;">
            <?php echo do_shortcode('[intensity-weather post_id="' . $found_post_id . '"]'); ?>
        </div>
        <div style="margin-bottom: 20px;">
            <?php echo do_shortcode('[trending_topics_box post_id="' . $found_post_id . '"]'); ?>
        </div>
        <div>
            <?php echo do_shortcode('[govbrief_quote post_id="' . $found_post_id . '"]'); ?>
        </div>
    </div>
    
    <button class="copy-button" onclick="downloadMetricsBlock1()">📥 Download Block 1 (Metrics)</button>
    <p style="font-size: 13px; color: #666; margin-top: 10px;">Intensity Score + Trending Topics + Quote</p>
    
    <div style="border-top: 1px solid #ddd; margin: 30px 0;"></div>
    
    <!-- Block 2: Most Read -->
    <div id="metrics-block-2" style="background: transparent; padding: 0; width: 600px;">
        <?php echo do_shortcode('[govbrief_most_read post_id="' . $found_post_id . '"]'); ?>
    </div>
    
    <button class="copy-button" onclick="downloadMetricsBlock2()">📥 Download Block 2 (Most Read)</button>
    <p style="font-size: 13px; color: #666; margin-top: 10px;">Yesterday's Most Read (copy the link manually from the button above)</p>
</div>

<?php 
    wp_reset_postdata();
endif; 
?>

<?php if (!$daily_post_found): ?>
<div class="links-section" style="margin-bottom: 30px; background: #fff3cd; border: 1px solid #ffc107;">
    <p style="color: #856404; margin: 0;"><strong>⚠️ No daily post found for <?php echo $title_date; ?></strong></p>
    <p style="color: #856404; margin: 10px 0 0 0; font-size: 14px;">Make sure you have a published post with the calendar_date field set to "<?php echo $title_date; ?>"</p>
</div>
<?php endif; ?>

<!-- Start export content wrapper -->
<div id="export-content">

<!-- Re-include header for export -->
<div class="header-section">
    <img src="data:image/png;base64,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" alt="GovBrief Logo" class="header-logo">
    <h1>Curated Headlines for <?php echo $title_date; ?></h1>
    <div class="issue-number">Issue #<?php echo $issue_number; ?></div>
</div>

<!-- Subscribe CTA -->
<div class="subscribe-cta">Subscribe free at GovBrief.today for data, trends, images, and search.</div>

<?php
$counter = 1;
$all_links = [];
$has_stories = false;

$total_stories = 0;

// Query the daily-headlines CPT by ACF headline_date field
foreach($categories as $category) {
    $args = [
        'post_type' => 'daily-headlines',
        'posts_per_page' => -1,
        'meta_query' => [
            'relation' => 'AND',
            [
                'key' => 'headline_date',
                'value' => $selected_date,
                'compare' => '=',
                'type' => 'DATE'
            ],
            [
                'key' => 'include_in_editions',
                'value' => 'national',
                'compare' => 'LIKE'
            ]
        ],
        'tax_query' => [
            [
                'taxonomy' => 'category',
                'field' => 'name',
                'terms' => $category,
            ]
        ],
        'orderby' => 'menu_order title',
        'order' => 'ASC'
    ];
    
    $posts = get_posts($args);
    
    if($posts && !empty($posts)) {
        $category_stories = [];
        
        // Filter for primary category only
        foreach($posts as $post) {
            // Check if this is the PRIMARY category using Yoast
            $primary_cat_id = get_post_meta($post->ID, '_yoast_wpseo_primary_category', true);
            
            if($primary_cat_id) {
                $primary_cat = get_category($primary_cat_id);
                if($primary_cat && $primary_cat->name === $category) {
                    $category_stories[] = $post;
                }
            } else {
                // Fallback: use first category if no Yoast primary
                $post_categories = wp_get_post_categories($post->ID);
                if(!empty($post_categories)) {
                    $first_cat = get_category($post_categories[0]);
                    if($first_cat && $first_cat->name === $category) {
                        $category_stories[] = $post;
                    }
                }
            }
        }
        
        // Only show category if it has stories
        if(!empty($category_stories)) {
            $has_stories = true;
            
            // Wrap each category in a section div for CSS spacing control
            echo '<div class="category-section">';
            
            // Get category color for full-width bar
            $cat_color = isset($category_colors[$category]) ? $category_colors[$category] : '#333';
            echo '<div class="category-title" style="background: ' . $cat_color . ';">' . strtoupper($category) . '</div>';
            
            foreach($category_stories as $post) {
                $title = $post->post_title;
                
                // Get the headline_link from ACF, fallback to permalink
                $link = get_field('headline_link', $post->ID);
                if(empty($link)) {
                    $link = get_permalink($post->ID);
                }
                
                // Get the source name
                $source = get_field('headline_source', $post->ID);
                
                echo '<div class="story-item">';
                // Orange number
                echo '<span class="story-number">' . $counter . '.</span> ' . $title;
                
                // Add source inline in parentheses if it exists
                if($source) {
                    echo ' <span class="inline-source">(' . $source . ')</span>';
                }
                
                echo '</div>';
                
                // Store link with its number
                $all_links[$counter] = $link;
                $counter++;
                $total_stories++;
            }
            
            echo '</div>'; // Close category-section
        }
    }
}

if(!$has_stories) {
    echo '<p style="text-align: center; color: #999; padding: 40px;">No stories found for ' . $date_obj->format('F j, Y') . '.<br>Make sure Daily Headlines have headline_date set to ' . $selected_date . ' and "National" checked in Include in Editions.</p>';
}
?>

<!-- Close export content wrapper -->
</div>

<?php if($has_stories): ?>
<!-- Divider between stories and links -->
<div class="divider"></div>

<!-- Links section for copying to first comment -->
<div class="links-section">
    <h2>Links for First Comment</h2>
    <div id="links-list">
<?php
foreach($all_links as $num => $link) {
    echo '<div class="link-item">[' . $num . '] ' . $link . '</div>';
}
?>
    </div>
    
    <button class="copy-button" onclick="copyLinks()">Copy Links to Clipboard</button>
    <button class="copy-button" onclick="copyStories()">Copy Stories Text</button>
    <button class="copy-button" onclick="window.print()">Print/Save as PDF</button>
</div>

<!-- Export buttons section -->
<div class="divider"></div>
<div class="links-section export-buttons">
    <h2>Export for Social Media</h2>
    <p>Click the button below to download the brief as one long PNG image strip that you can crop as needed.</p>
    
    <button class="copy-button" onclick="downloadAsImage()">📥 Download as Image (PNG)</button>
    <p style="margin-top: 15px; font-size: 13px; color: #666;">Total: <?php echo $total_stories; ?> stories. Image will be exported as one continuous vertical strip.</p>
</div>

<!-- Rich HTML section for Substack -->
<div class="divider"></div>
<div class="links-section">
    <h2>For Substack Email (Copy & Paste)</h2>
    <div id="substack-content">
<?php
// Generate HTML with linked first three words that will work in Substack
$story_counter = 1;

foreach($categories as $category) {
    $args = [
        'post_type' => 'daily-headlines',
        'posts_per_page' => -1,
        'meta_query' => [
            'relation' => 'AND',
            [
                'key' => 'headline_date',
                'value' => $selected_date,
                'compare' => '=',
                'type' => 'DATE'
            ],
            [
                'key' => 'include_in_editions',
                'value' => 'national',
                'compare' => 'LIKE'
            ]
        ],
        'tax_query' => [
            [
                'taxonomy' => 'category',
                'field' => 'name',
                'terms' => $category,
            ]
        ],
        'orderby' => 'menu_order title',
        'order' => 'ASC'
    ];
    
    $posts = get_posts($args);
    
    if($posts && !empty($posts)) {
        $category_stories = [];
        
        foreach($posts as $post) {
            $primary_cat_id = get_post_meta($post->ID, '_yoast_wpseo_primary_category', true);
            
            if($primary_cat_id) {
                $primary_cat = get_category($primary_cat_id);
                if($primary_cat && $primary_cat->name === $category) {
                    $category_stories[] = $post;
                }
            } else {
                $post_categories = wp_get_post_categories($post->ID);
                if(!empty($post_categories)) {
                    $first_cat = get_category($post_categories[0]);
                    if($first_cat && $first_cat->name === $category) {
                        $category_stories[] = $post;
                    }
                }
            }
        }
        
        if(!empty($category_stories)) {
            echo '<p><strong>' . strtoupper($category) . '</strong></p>';
            
            foreach($category_stories as $post) {
                $title = $post->post_title;
                $link = get_field('headline_link', $post->ID);
                if(empty($link)) {
                    $link = get_permalink($post->ID);
                }
                
                // Add UTM parameters for Substack tracking
                $link = add_query_arg(array(
                    'utm_source'   => 'substack',
                    'utm_medium'   => 'email',
                    'utm_campaign' => 'daily_headlines'
                ), $link);
                $source = get_field('headline_source', $post->ID);
                
                // Split title into words
                $words = explode(' ', $title);
                $first_three = array_slice($words, 0, 3);
                $rest = array_slice($words, 3);
                
                echo '<p>' . $story_counter . '. ';
                echo '<a href="' . esc_url($link) . '">' . esc_html(implode(' ', $first_three)) . '</a>';
                if(count($rest) > 0) {
                    echo ' ' . esc_html(implode(' ', $rest));
                }
                
                if($source) {
                    echo ' - <em>' . esc_html($source) . '</em>';
                }
                
                echo '</p>';
                $story_counter++;
            }
        }
    }
}
?>
    </div>
    
    <button class="copy-button" onclick="copySubstackContent()">Copy for Substack</button>
</div>

<?php endif; ?>

<!-- JavaScript -->
<script>
function setDate(date) {
    document.getElementById('date-picker').value = date;
    document.querySelector('form').submit();
}

function copyLinks() {
    let linksText = '';
    <?php foreach($all_links as $num => $link): ?>
    linksText += '[<?php echo $num; ?>] <?php echo $link; ?>\n';
    <?php endforeach; ?>
    
    navigator.clipboard.writeText(linksText).then(function() {
        alert('Links copied to clipboard!');
    }, function(err) {
        // Fallback for older browsers
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
    // Get just the stories content including sources
    const storyElements = document.querySelectorAll('.category-title, .story-item');
    let storiesText = 'Curated Headlines for <?php echo $title_date; ?>\n';
    storiesText += 'Issue #<?php echo $issue_number; ?>\n\n';
    storiesText += 'Subscribe free at GovBrief.today for data, trends, images, and search.\n\n';
    
    storyElements.forEach(function(element) {
        storiesText += element.innerText + '\n';
        if(element.className === 'story-item') {
            storiesText += '\n'; // Extra line break after each story
        }
    });
    
    navigator.clipboard.writeText(storiesText).then(function() {
        alert('Stories copied to clipboard!');
    }, function(err) {
        // Fallback
        const textArea = document.createElement('textarea');
        textArea.value = storiesText;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        alert('Stories copied to clipboard!');
    });
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

<!-- HTML2Canvas Library for Image Export -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<script>
// Download export content as PNG image
function downloadAsImage() {
    const exportContent = document.getElementById('export-content');
    const dateSelector = document.querySelector('.date-selector');
    const linksSection = document.querySelectorAll('.links-section');
    const exportButtons = document.querySelector('.export-buttons');
    
    // Hide elements we don't want in the image
    if(dateSelector) dateSelector.style.display = 'none';
    linksSection.forEach(section => section.style.display = 'none');
    
    // Show loading message
    const loadingMsg = document.createElement('div');
    loadingMsg.innerHTML = 'Generating images... please wait...';
    loadingMsg.style.cssText = 'position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: rgba(0,0,0,0.8); color: white; padding: 20px; border-radius: 10px; z-index: 9999; font-size: 18px;';
    document.body.appendChild(loadingMsg);
    
    const dateStr = '<?php echo $display_date; ?>';
    
    // Use html2canvas to capture the content
    html2canvas(exportContent, {
        scale: 2,
        backgroundColor: '#ffffff',
        logging: false,
        useCORS: true,
        allowTaint: true,
        width: exportContent.scrollWidth,
        height: exportContent.scrollHeight,
        windowWidth: exportContent.scrollWidth,
        windowHeight: exportContent.scrollHeight
    }).then(canvas => {
        // Download clean image
        canvas.toBlob(function(blob) {
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.download = dateStr + '-govbrief-social.png';
            link.href = url;
            link.click();
            URL.revokeObjectURL(url);
            
            // Now create reference image with pixel markers
            loadingMsg.innerHTML = 'Creating reference image...';
            createReferenceImage(canvas, dateStr);
        }, 'image/png');
    }).catch(error => {
        console.error('Error generating image:', error);
        document.body.removeChild(loadingMsg);
        alert('Error generating image. Please try the Print/PDF option instead.');
        
        // Restore hidden elements
        if(dateSelector) dateSelector.style.display = '';
        linksSection.forEach(section => section.style.display = '');
    });
}

function createReferenceImage(originalCanvas, dateStr) {
    const loadingMsg = document.querySelector('div[style*="Generating"]');
    
    // Create new canvas for reference image
    const refCanvas = document.createElement('canvas');
    refCanvas.width = originalCanvas.width + 200; // Add space for ruler on left
    refCanvas.height = originalCanvas.height;
    const ctx = refCanvas.getContext('2d');
    
    // Draw original image offset to make room for ruler
    ctx.drawImage(originalCanvas, 200, 0);
    
    // Draw ruler on the left
    ctx.fillStyle = '#f0f0f0';
    ctx.fillRect(0, 0, 200, refCanvas.height);
    
    // Draw pixel markers - using actual file pixel coordinates
    ctx.font = 'bold 32px Arial';
    ctx.textAlign = 'right';
    
    // Major marks every 500px
    ctx.strokeStyle = '#000';
    ctx.lineWidth = 3;
    for(let y = 0; y < refCanvas.height; y += 500) {
        ctx.beginPath();
        ctx.moveTo(150, y);
        ctx.lineTo(200, y);
        ctx.stroke();
        
        ctx.fillStyle = '#000';
        ctx.fillText(y + 'px', 140, y + 12);
    }
    
    // Minor marks every 100px
    ctx.strokeStyle = '#666';
    ctx.lineWidth = 2;
    ctx.font = '20px Arial';
    for(let y = 100; y < refCanvas.height; y += 100) {
        if(y % 500 !== 0) { // Skip major marks
            ctx.beginPath();
            ctx.moveTo(170, y);
            ctx.lineTo(200, y);
            ctx.stroke();
            
            ctx.fillStyle = '#666';
            ctx.fillText(y, 160, y + 8);
        }
    }
    
    // Highlight sweet spot zones (1400-1800, 2900-3300, etc) in actual file pixels
    ctx.fillStyle = 'rgba(0, 255, 0, 0.15)';
    for(let baseY = 1400; baseY < refCanvas.height; baseY += 1600) {
        ctx.fillRect(0, baseY, 200, 400);
    }
    
    // Download reference image
    refCanvas.toBlob(function(blob) {
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.download = dateStr + '-govbrief-REFERENCE.png';
        link.href = url;
        link.click();
        URL.revokeObjectURL(url);
        
        // Cleanup
        document.body.removeChild(loadingMsg);
        
        // Restore hidden elements
        const dateSelector = document.querySelector('.date-selector');
        const linksSection = document.querySelectorAll('.links-section');
        if(dateSelector) dateSelector.style.display = '';
        linksSection.forEach(section => section.style.display = '');
        
        alert('Both images downloaded!\n\n1. Clean image for posting\n2. Reference image with pixel markers\n\nLook at the reference to decide split points, then edit splits.txt');
    }, 'image/png');
}

// Download metrics block 1 (Intensity + Trending + Quote) with transparent gaps
function downloadMetricsBlock1() {
    const block = document.getElementById('metrics-block-1');
    const dateStr = '<?php echo $display_date; ?>';
    
    // Save original width and temporarily expand for capture
    const originalWidth = block.style.width;
    block.style.width = '800px';
    
    // Force reflow/repaint so browser recalculates layout
    void block.offsetHeight;
    
    // Show loading message
    const loadingMsg = document.createElement('div');
    loadingMsg.innerHTML = 'Generating Block 1 image...';
    loadingMsg.style.cssText = 'position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: rgba(0,0,0,0.8); color: white; padding: 20px; border-radius: 10px; z-index: 9999; font-size: 18px;';
    document.body.appendChild(loadingMsg);
    
    html2canvas(block, {
        scale: 1.5,
        backgroundColor: null, // Transparent background!
        logging: false,
        useCORS: true,
        allowTaint: true
    }).then(canvas => {
        // Restore original width immediately after capture
        block.style.width = originalWidth;
        canvas.toBlob(function(blob) {
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.download = dateStr + '-substack-block1-metrics.png';
            link.href = url;
            link.click();
            URL.revokeObjectURL(url);
            
            document.body.removeChild(loadingMsg);
            alert('Block 1 downloaded! (Intensity + Trending + Quote)\n\nThe gaps are transparent and will blend with your Substack background.');
        }, 'image/png');
    }).catch(error => {
        console.error('Error generating Block 1:', error);
        document.body.removeChild(loadingMsg);
        alert('Error generating image. Please try again.');
    });
}

// Download metrics block 2 (Most Read) with transparent background
function downloadMetricsBlock2() {
    const block = document.getElementById('metrics-block-2');
    
    if (!block) {
        alert('Error: Could not find Block 2 element');
        return;
    }
    
    const dateStr = '<?php echo $display_date; ?>';
    
    // Save original width and temporarily expand for capture
    const originalWidth = block.style.width;
    block.style.width = '800px';
    
    // Force reflow/repaint so browser recalculates layout
    void block.offsetHeight;
    
    // Show loading message
    const loadingMsg = document.createElement('div');
    loadingMsg.innerHTML = 'Generating Block 2 image...';
    loadingMsg.style.cssText = 'position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: rgba(0,0,0,0.8); color: white; padding: 20px; border-radius: 10px; z-index: 9999; font-size: 18px;';
    document.body.appendChild(loadingMsg);
    
    html2canvas(block, {
        scale: 1.5,
        backgroundColor: null,
        logging: true,
        useCORS: true,
        allowTaint: true
    }).then(canvas => {
        // Restore original width immediately after capture
        block.style.width = originalWidth;
        if (!canvas) {
            document.body.removeChild(loadingMsg);
            alert('Error: Canvas creation failed. Check browser console for details.');
            return;
        }
        
        canvas.toBlob(function(blob) {
            if (!blob) {
                document.body.removeChild(loadingMsg);
                alert('Error: Could not create image blob. Check browser console for details.');
                return;
            }
            
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.download = dateStr + '-substack-block2-mostread.png';
            link.href = url;
            link.click();
            URL.revokeObjectURL(url);
            
            document.body.removeChild(loadingMsg);
            alert('Block 2 downloaded! (Most Read)\n\nRemember to copy the link URL manually from the button in the box above.');
        }, 'image/png');
    }).catch(error => {
        console.error('Error generating Block 2:', error);
        document.body.removeChild(loadingMsg);
        alert('Error generating image: ' + error.message + '\n\nCheck browser console for details.');
    });
}
</script>

</body>
</html>
