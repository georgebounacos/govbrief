<?php
/**
 * Template Name: GovBrief New Jersey
 */
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GovBrief New Jersey Edition</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            padding: 20px;
        }

        .nj-date-picker {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            max-width: 1400px;
            margin-left: auto;
            margin-right: auto;
        }

        .date-picker-container {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
            flex-wrap: wrap;
        }

        .date-picker-container label {
            font-weight: 600;
            color: #1e293b;
        }

        #nj-date-input {
            padding: 8px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            font-size: 14px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.4);
        }

        .btn-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            margin-left: 10px;
        }

        .btn-success:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
        }

        .quick-select {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .quick-select span {
            font-weight: 600;
            color: #64748b;
        }

        .quick-date-btn {
            background: #64748b;
            color: white;
            border: none;
            padding: 6px 16px;
            border-radius: 4px;
            font-size: 13px;
            cursor: pointer;
            transition: background 0.2s;
        }

        .quick-date-btn:hover {
            background: #475569;
        }

        .export-buttons {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #e2e8f0;
        }

        .nj-page-wrapper {
            max-width: 1400px;
            margin: 0 auto;
        }

        .nj-header {
            text-align: center;
            margin-bottom: 30px;
            background: white;
            padding: 30px 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .header-logo {
            max-width: 400px;
            height: auto;
            margin-bottom: 20px;
        }

        .nj-title {
            font-size: 48px;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .nj-badge {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            color: #78350f;
            padding: 8px 20px;
            border-radius: 20px;
            font-size: 16px;
            font-weight: 700;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 8px rgba(251, 191, 36, 0.3);
        }

        .nj-date {
            font-size: 24px;
            color: #475569;
            font-weight: 400;
        }

        .nj-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-item {
            background: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .stat-number {
            display: block;
            font-size: 36px;
            font-weight: 800;
            color: #2563eb;
            margin-bottom: 5px;
        }

        .stat-label {
            display: block;
            font-size: 14px;
            color: #64748b;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .nj-stories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(450px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .nj-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            display: flex;
            flex-direction: column;
        }

        .nj-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(0,0,0,0.15);
        }

        .card-header {
            padding: 12px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
            font-weight: 700;
        }

        .category-name {
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .story-number {
            font-size: 18px;
            font-weight: 800;
        }

        .card-body {
            padding: 20px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .card-headline {
            font-size: 18px;
            line-height: 1.4;
            margin-bottom: 15px;
            font-weight: 700;
            flex-grow: 1;
        }

        .card-headline a {
            color: #1e293b;
            text-decoration: none;
            transition: color 0.2s;
        }

        .card-headline a:hover {
            color: #2563eb;
        }

        .card-callout {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            border-left: 4px solid #2563eb;
            padding: 12px 16px;
            margin-bottom: 15px;
            border-radius: 4px;
            font-size: 16px;
            font-weight: 600;
            color: #1e40af;
        }

        .card-source {
            font-size: 13px;
            color: #64748b;
            padding-top: 12px;
            border-top: 1px solid #e2e8f0;
            margin-top: auto;
        }

        .card-source strong {
            color: #475569;
        }

        .card-date {
            font-size: 13px;
            color: #64748b;
            padding-top: 4px;
            text-align: left;
        }

        .nj-no-stories {
            background: white;
            padding: 60px 40px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .nj-no-stories p {
            font-size: 18px;
            color: #64748b;
            margin: 0 0 10px 0;
        }

        @media (max-width: 968px) {
            .nj-stories-grid {
                grid-template-columns: 1fr;
            }
            
            .nj-title {
                font-size: 36px;
            }
            
            .nj-date {
                font-size: 20px;
            }
        }

        @media (max-width: 640px) {
            body {
                padding: 15px;
            }
            
            .nj-title {
                font-size: 28px;
                flex-direction: column;
                gap: 10px;
            }
            
            .nj-badge {
                font-size: 14px;
            }
            
            .date-picker-container {
                flex-direction: column;
                align-items: stretch;
            }
            
            .quick-select {
                flex-direction: column;
                align-items: stretch;
            }
            
            .quick-date-btn {
                width: 100%;
            }
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }

        .modal-content {
            background-color: white;
            margin: 15% auto;
            padding: 30px;
            border-radius: 12px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        }

        .modal-content h2 {
            margin-bottom: 20px;
            color: #1e293b;
        }

        .modal-content p {
            margin-bottom: 20px;
            color: #475569;
            line-height: 1.6;
        }

        .modal-content input {
            width: 100%;
            padding: 10px;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            font-size: 16px;
            margin-bottom: 20px;
        }

        .modal-buttons {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }

        .btn-cancel {
            background: #94a3b8;
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
        }

        .btn-cancel:hover {
            background: #64748b;
        }
    </style>
</head>
<body>

<?php
$selected_date = isset($_GET['date']) ? sanitize_text_field($_GET['date']) : date('Y-m-d');
$display_date = date('F j, Y', strtotime($selected_date));

// Get current issue number
$current_issue = govbrief_get_nj_issue_number($selected_date);
$next_issue = govbrief_get_next_nj_issue_number();

// Build query
$args = array(
    'post_type' => 'daily-headlines',
    'posts_per_page' => -1,
    'post_status' => 'publish',
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

$stories_by_category = array();
$nj_count = 0;
$federal_count = 0;

if ($headlines->have_posts()) {
    while ($headlines->have_posts()) {
        $headlines->the_post();
        
        $primary_category = get_post_meta(get_the_ID(), '_yoast_wpseo_primary_category', true);
        $category = get_category($primary_category);
        $category_name = $category ? $category->name : 'Uncategorized';
        
        $title = get_the_title();
        $is_nj_story = (stripos($title, 'new jersey') !== false || stripos($title, ' nj ') !== false);
        
        if (!isset($stories_by_category[$category_name])) {
            $stories_by_category[$category_name] = array();
        }
        
        $story_data = array(
            'title' => $title,
            'source' => get_field('headline_source'),
            'link' => get_field('headline_link'),
            'callout' => get_field('story_callout'),
            'is_nj_specific' => $is_nj_story
        );
        
        $stories_by_category[$category_name][] = $story_data;
        
        if ($is_nj_story) {
            $nj_count++;
        } else {
            $federal_count++;
        }
    }
    wp_reset_postdata();
}

$total_stories = $nj_count + $federal_count;

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
?>

<div class="nj-date-picker">
    <div class="date-picker-container">
        <label for="nj-date-input">Select Date:</label>
        <input type="date" id="nj-date-input" value="<?php echo $selected_date; ?>">
        <button id="generate-brief-btn" class="btn-primary">Generate Brief</button>
    </div>
    
    <div class="quick-select">
        <span>Quick select:</span>
        <button class="quick-date-btn" data-days="0">Today</button>
        <button class="quick-date-btn" data-days="1">Yesterday</button>
        <button class="quick-date-btn" data-days="2">2 Days Ago</button>
        <button class="quick-date-btn" data-days="3">3 Days Ago</button>
        <button class="quick-date-btn" data-days="7">1 Week Ago</button>
    </div>

    <?php if ($total_stories > 0): ?>
    <div class="export-buttons">
        <button id="export-social-btn" class="btn-success">📱 Generate Social Post</button>
        <button id="export-substack-btn" class="btn-success">📧 Generate Substack</button>
        <button id="create-brief-btn" class="btn-success">📄 Create NJ Brief Post</button>
    </div>
    <?php endif; ?>
</div>

<div class="nj-page-wrapper">
    <div class="nj-header">
        <img src="https://govbrief.today/wp-content/uploads/2025/09/https___substack-post-media.s3.amazonaws.com_public_images_de5c2270-092f-47b4-8d6c-1db9ab80f506_3072x1024.png" alt="GovBrief Logo" class="header-logo">
        <div class="nj-title">
            <span class="nj-badge">NEW JERSEY EDITION</span>
        </div>
        <h2 class="nj-date">Curated Headlines for <?php echo $display_date; ?></h2>
        <?php if ($current_issue): ?>
        <p style="color: #64748b; margin-top: 10px;">Issue #<?php echo $current_issue; ?></p>
        <?php endif; ?>
    </div>

    <div class="nj-stats">
        <div class="stat-item">
            <span class="stat-number"><?php echo $total_stories; ?></span>
            <span class="stat-label">Total Stories</span>
        </div>
        <div class="stat-item">
            <span class="stat-number"><?php echo $nj_count; ?></span>
            <span class="stat-label">NJ Specific</span>
        </div>
        <div class="stat-item">
            <span class="stat-number"><?php echo $federal_count; ?></span>
            <span class="stat-label">Federal Impact</span>
        </div>
    </div>

    <?php if ($total_stories > 0): ?>
        <div class="nj-stories-grid">
            <?php 
            $story_counter = 1;
            $category_order = array('Extremism', 'Dissent', 'Disaster Relief', 'Foreign Relations', 'War', 'Health', 'Human Rights', 'Environment', 'Science', 'DEI', 'Voting Rights', 'Censorship', 'Economy', 'Military', 'Intelligence', 'Courts', 'Criminal Justice', 'Social Security', 'Immigration', 'Education', 'Oversight', 'Congress', 'Federal Personnel', 'Transportation', 'Data', 'Propaganda', 'Religion', 'Media', 'Arts', 'Grift', 'Protest', 'Fighting Back');
            
            foreach ($category_order as $cat_name) {
                if (!isset($stories_by_category[$cat_name])) continue;
                
                $color = isset($category_colors[$cat_name]) ? $category_colors[$cat_name] : '#6b7280';
                
                foreach ($stories_by_category[$cat_name] as $story) {
                    ?>
                    <div class="nj-card">
                        <div class="card-header" style="background-color: <?php echo $color; ?>;">
                            <span class="category-name"><?php echo $cat_name; ?></span>
                            <span class="story-number">#<?php echo $story_counter; ?></span>
                        </div>
                        
                        <div class="card-body">
                            <h3 class="card-headline">
                                <a href="<?php echo esc_url($story['link']); ?>" target="_blank" rel="noopener">
                                    <?php echo esc_html($story['title']); ?>
                                </a>
                            </h3>
                            
                            <?php if (!empty($story['callout'])): ?>
                                <div class="card-callout">
                                    <?php echo esc_html($story['callout']); ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="card-source">
                                <strong>SOURCE:</strong> <?php echo esc_html($story['source']); ?>
                                <div class="card-date">Appears in GovBrief.Today on <?php echo $display_date; ?></div>
                            </div>
                        </div>
                    </div>
                    <?php
                    $story_counter++;
                }
            }
            ?>
        </div>
    <?php else: ?>
        <div class="nj-no-stories">
            <p>No stories found for <?php echo $display_date; ?>.</p>
            <p style="font-size: 14px; color: #999; margin-top: 10px;">Check "New Jersey" in Include in Editions on daily-headlines posts for this date.</p>
        </div>
    <?php endif; ?>
</div>

<div id="issue-modal" class="modal">
    <div class="modal-content">
        <h2>Set Issue Number</h2>
        <p id="modal-message"></p>
        <input type="number" id="issue-number-input" min="1" />
        <div class="modal-buttons">
            <button class="btn-cancel" onclick="closeModal()">Cancel</button>
            <button class="btn-primary" onclick="confirmIssueNumber()">Confirm</button>
        </div>
    </div>
</div>

<script>
var selectedDate = '<?php echo $selected_date; ?>';
var currentIssue = <?php echo $current_issue ? $current_issue : 'null'; ?>;
var nextIssue = <?php echo $next_issue; ?>;
var exportType = '';

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('generate-brief-btn').addEventListener('click', function() {
        var selectedDate = document.getElementById('nj-date-input').value;
        window.location.href = window.location.pathname + '?date=' + selectedDate;
    });
    
    var quickBtns = document.querySelectorAll('.quick-date-btn');
    quickBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            var daysAgo = this.getAttribute('data-days');
            var date = new Date();
            date.setDate(date.getDate() - daysAgo);
            var dateString = date.toISOString().split('T')[0];
            window.location.href = window.location.pathname + '?date=' + dateString;
        });
    });
    
    document.getElementById('nj-date-input').addEventListener('keypress', function(e) {
        if (e.which === 13 || e.keyCode === 13) {
            document.getElementById('generate-brief-btn').click();
        }
    });

    var socialBtn = document.getElementById('export-social-btn');
    if (socialBtn) {
        socialBtn.addEventListener('click', function() {
            exportType = 'social';
            if (currentIssue) {
                openExport('social', currentIssue);
            } else {
                showIssueModal('This will be NJ Edition #' + nextIssue + '. Is this correct?', nextIssue);
            }
        });
    }

    var substackBtn = document.getElementById('export-substack-btn');
    if (substackBtn) {
        substackBtn.addEventListener('click', function() {
            exportType = 'substack';
            if (currentIssue) {
                openExport('substack', currentIssue);
            } else {
                showIssueModal('This will be NJ Edition #' + nextIssue + '. Is this correct?', nextIssue);
            }
        });
    }
});

function showIssueModal(message, suggestedNumber) {
    document.getElementById('modal-message').textContent = message;
    document.getElementById('issue-number-input').value = suggestedNumber;
    document.getElementById('issue-modal').style.display = 'block';
}

function closeModal() {
    document.getElementById('issue-modal').style.display = 'none';
}

function confirmIssueNumber() {
    var issueNumber = parseInt(document.getElementById('issue-number-input').value);
    
    var formData = new FormData();
    formData.append('action', 'set_nj_issue_number');
    formData.append('nonce', '<?php echo wp_create_nonce("govbrief_nj_nonce"); ?>');
    formData.append('date', selectedDate);
    formData.append('issue_number', issueNumber);
    
    fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeModal();
            openExport(exportType, issueNumber);
        }
    });
}

function openExport(type, issueNumber) {
    var url = window.location.origin + '/nj-internal-' + type + '/?date=' + selectedDate + '&issue=' + issueNumber;
    window.open(url, '_blank');
}
var createBriefBtn = document.getElementById('create-brief-btn');
if (createBriefBtn) {
    createBriefBtn.addEventListener('click', function() {
        if (confirm('Create a new NJ Brief post for ' + selectedDate + '?')) {
            // Redirect to create the post
            var createUrl = '<?php echo admin_url("admin-ajax.php"); ?>?action=create_nj_brief_post&date=' + selectedDate + '&nonce=<?php echo wp_create_nonce("create_nj_brief"); ?>';
            window.location.href = createUrl;
        }
    });
}
</script>

</body>
</html>