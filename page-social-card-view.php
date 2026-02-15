<?php
/**
 * Template Name: Social Card View Generator
 */

// Get parameters
$selected_date = isset($_GET['date']) ? sanitize_text_field($_GET['date']) : date('Y-m-d');

$date_obj = DateTime::createFromFormat('Y-m-d', $selected_date);
$display_date = $date_obj->format('ymd'); // 251011 format

// Your categories in priority order
$categories = array(
    'Extremism', 'Dissent', 'Disaster Relief', 'Foreign Relations', 'War', 'Health',
    'Human Rights', 'Epstein Sex Crime Network', 'Environment', 'Science', 'DEI', 'Voting Rights', 'Censorship',
    'Economy', 'Military', 'Intelligence', 'Courts', 'Criminal Justice', 'Social Security',
    'Immigration', 'Education', 'Oversight', 'Congress', 'Federal Personnel', 'Transportation',
    'Data', 'Propaganda', 'Religion', 'Media', 'Arts', 'Grift', 'Protest', 'Fighting Back'
);

// Category colors
$category_colors = array(
    'Extremism' => '#dc2626', 'Dissent' => '#ea580c', 'Disaster Relief' => '#d97706',
    'Foreign Relations' => '#2563eb', 'War' => '#7c2d12', 'Health' => '#16a34a',
    'Human Rights' => '#db2777', 'Epstein Sex Crime Network' => '#7f1d1d', 'Environment' => '#059669', 'Science' => '#0891b2',
    'DEI' => '#7c3aed', 'Voting Rights' => '#4f46e5', 'Censorship' => '#dc2626',
    'Economy' => '#16a34a', 'Military' => '#475569', 'Intelligence' => '#1e293b',
    'Courts' => '#7c3aed', 'Criminal Justice' => '#be123c', 'Social Security' => '#0d9488',
    'Immigration' => '#ea580c', 'Education' => '#2563eb', 'Oversight' => '#64748b',
    'Congress' => '#1e40af', 'Federal Personnel' => '#6366f1', 'Transportation' => '#0891b2',
    'Data' => '#6b7280', 'Propaganda' => '#dc2626', 'Religion' => '#7c3aed',
    'Media' => '#059669', 'Arts' => '#db2777', 'Grift' => '#b91c1c',
    'Protest' => '#ea580c', 'Fighting Back' => '#16a34a'
);

?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $display_date; ?> - Social Card View</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .date-selector {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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
        .export-controls {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .export-button {
            background: #16a34a;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            margin: 0 10px;
        }
        .export-button:hover {
            background: #15803d;
        }
        
        /* Card Container - this is what gets exported */
        #card-container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            width: 1200px;
            margin: 0 auto 20px auto;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #333;
        }
        .header h1 {
            margin: 0 0 5px 0;
            font-size: 32px;
            font-weight: 900;
        }
        .header .date {
            font-size: 18px;
            color: #666;
            font-weight: 600;
        }
        
        /* Card Grid - 2 columns */
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 20px;
        }
        
        /* Individual Card */
        .story-card {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border: 1px solid #e5e7eb;
            break-inside: avoid;
            page-break-inside: avoid;
            -webkit-column-break-inside: avoid;
        }
        .category-bar {
            padding: 8px 15px;
            color: white;
            font-weight: 700;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .story-number {
            font-size: 16px;
            font-weight: 900;
        }
        .card-content {
            padding: 15px;
        }
        .story-headline {
            font-size: 15px;
            font-weight: 600;
            line-height: 1.3;
            color: #1a1a1a;
            margin-bottom: 8px;
        }
        .story-source {
            font-size: 12px;
            color: #666;
            font-style: italic;
        }
        
        /* Links Section */
        .links-section {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .link-item {
            margin-bottom: 5px;
            font-size: 13px;
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
            body {
                background: white;
            }
            .date-selector, .export-controls, .links-section {
                display: none;
            }
            #card-container {
                box-shadow: none;
            }
            .story-card {
                break-inside: avoid;
                page-break-inside: avoid;
            }
        }
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
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

<!-- Export Controls -->
<div class="export-controls">
    <h3 style="margin-top: 0;">Export Options</h3>
    <button class="export-button" onclick="exportAsPNG()">📥 Download as PNG</button>
    <button class="copy-button" onclick="copyLinks()">Copy Links for Comment</button>
</div>

<!-- Card Container (This gets exported) -->
<div id="card-container">
    <div class="header">
        <h1><?php echo $display_date; ?> - Happened Today</h1>
        <div class="date">GovBrief National Edition</div>
    </div>
    
    <div class="cards-grid">
<?php
$counter = 1;
$all_links = array();

// Query stories from national edition
foreach ($categories as $category) {
    $args = array(
        'post_type' => 'daily-headlines',
        'posts_per_page' => -1,
        'meta_query' => array(
            'relation' => 'AND',
            array(
                'key' => 'headline_date',
                'value' => $selected_date,
                'compare' => '=',
                'type' => 'DATE'
            ),
            array(
                'key' => 'include_in_editions',
                'value' => 'national',
                'compare' => 'LIKE'
            )
        ),
        'tax_query' => array(
            array(
                'taxonomy' => 'category',
                'field' => 'name',
                'terms' => $category,
            )
        ),
        'orderby' => 'menu_order title',
        'order' => 'ASC'
    );
    
    $posts = get_posts($args);
    
    if ($posts && !empty($posts)) {
        foreach ($posts as $post) {
            // Check if this is the PRIMARY category
            $primary_cat_id = get_post_meta($post->ID, '_yoast_wpseo_primary_category', true);
            
            if ($primary_cat_id) {
                $primary_cat = get_category($primary_cat_id);
                if ($primary_cat && $primary_cat->name !== $category) {
                    continue;
                }
            } else {
                $post_categories = wp_get_post_categories($post->ID);
                if (!empty($post_categories)) {
                    $first_cat = get_category($post_categories[0]);
                    if ($first_cat && $first_cat->name !== $category) {
                        continue;
                    }
                }
            }
            
            $title = $post->post_title;
            $link = get_field('headline_link', $post->ID);
            if (empty($link)) {
                $link = get_permalink($post->ID);
            }
            $source = get_field('headline_source', $post->ID);
            $color = isset($category_colors[$category]) ? $category_colors[$category] : '#6b7280';
            
            // Store link for later
            $all_links[$counter] = $link;
            ?>
            <div class="story-card">
                <div class="category-bar" style="background-color: <?php echo $color; ?>;">
                    <span><?php echo strtoupper($category); ?></span>
                    <span class="story-number"><?php echo $counter; ?></span>
                </div>
                <div class="card-content">
                    <div class="story-headline"><?php echo esc_html($title); ?></div>
                    <?php if ($source): ?>
                        <div class="story-source"><?php echo esc_html($source); ?></div>
                    <?php endif; ?>
                </div>
            </div>
            <?php
            $counter++;
        }
    }
}

if ($counter === 1) {
    echo '<div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: #999;">No stories found for ' . $date_obj->format('F j, Y') . '</div>';
}
?>
    </div>
</div>

<!-- Links Section -->
<?php if ($counter > 1): ?>
<div class="links-section">
    <h3>Links for First Comment</h3>
    <div id="links-list">
<?php
foreach ($all_links as $num => $link) {
    echo '<div class="link-item">[' . $num . '] ' . esc_url($link) . '</div>';
}
?>
    </div>
</div>
<?php endif; ?>

<script>
function setDate(date) {
    window.location.href = '?date=' + date;
}

function exportAsPNG() {
    const button = event.target;
    button.textContent = '⏳ Generating...';
    button.disabled = true;
    
    html2canvas(document.getElementById('card-container'), {
        scale: 2,
        backgroundColor: '#ffffff',
        logging: false,
        windowWidth: 1260
    }).then(canvas => {
        canvas.toBlob(blob => {
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.download = 'govbrief-<?php echo $display_date; ?>.png';
            link.href = url;
            link.click();
            URL.revokeObjectURL(url);
            
            button.textContent = '📥 Download as PNG';
            button.disabled = false;
        });
    });
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
</script>

</body>
</html>