<?php
/**
 * Template Name: Hero Cards Generator
 */

// Get date from query parameter or default to today
$selected_date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$date_obj = DateTime::createFromFormat('Y-m-d', $selected_date);
$display_date = $date_obj->format('F j, Y');

// Category colors (keeping for potential future use)
$category_colors = array(
    'Extremism' => '#dc2626', 'Dissent' => '#ea580c', 'Disaster Relief' => '#f59e0b',
    'Foreign Relations' => '#0891b2', 'War' => '#7c2d12', 'Health' => '#16a34a',
    'Human Rights' => '#db2777', 'Epstein Sex Crime Network' => '#7f1d1d',
    'Environment' => '#059669', 'Science' => '#0284c7',
    'DEI' => '#7c3aed', 'Voting Rights' => '#6366f1', 'Censorship' => '#ef4444',
    'Economy' => '#10b981', 'Military' => '#78716c', 'Intelligence' => '#475569',
    'Justice Dept' => '#4338ca', 'Courts' => '#7c3aed', 'Criminal Justice' => '#8b5cf6', 'Social Security' => '#e11d48',
    'Immigration' => '#f43f5e', 'Education' => '#f59e0b', 'Oversight' => '#84cc16',
    'Congress' => '#2563eb', 'Federal Personnel' => '#10b981', 'Transportation' => '#14b8a6',
    'Data' => '#06b6d4', 'Propaganda' => '#0ea5e9', 'Religion' => '#3b82f6',
    'Media' => '#6366f1', 'Arts' => '#8b5cf6', 'Grift' => '#a78bfa',
    'Protest' => '#ec4899', 'Fighting Back' => '#f472b6'
);

get_header();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hero Cards - <?php echo $display_date; ?></title>
    
    <!-- html2canvas for PNG export -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    
    <style>
        body {
            background: #f5f5f5;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            padding: 20px;
            margin: 0;
        }
        
        .hero-page-container {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        /* Date Picker */
        .date-picker-section {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .date-picker-section h2 {
            margin: 0 0 15px 0;
            font-size: 24px;
        }
        
        .date-controls {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .date-controls input[type="date"] {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        
        .date-controls button {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
        }
        
        .btn-primary {
            background: #2563eb;
            color: white;
        }
        
        .btn-primary:hover {
            background: #1d4ed8;
        }
        
        .quick-date {
            background: #f3f4f6;
            color: #374151;
        }
        
        .quick-date:hover {
            background: #e5e7eb;
        }
        
        /* Hero Cards Grid */
        .hero-cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 30px;
            margin-top: 20px;
        }
        
        .hero-card-wrapper {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .hero-card-wrapper h3 {
            margin: 0 0 15px 0;
            font-size: 18px;
            color: #374151;
        }
        
        /* The actual hero card - 9:16 ratio - BLACK AND WHITE */
        .hero-card {
            width: 360px;
            height: 640px;
            background: #000000; /* Solid black background */
            border-radius: 16px;
            padding: 40px 30px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            color: white;
            position: relative;
            overflow: hidden;
            margin: 0 auto 15px auto;
        }
        
        /* Full size for export (1080x1920) */
        .hero-card-export {
            width: 1080px;
            height: 1920px;
            padding: 120px 90px;
        }
        
        /* Logo with date */
        .hero-logo {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .hero-logo-main {
            font-size: 28px;
            font-weight: 900;
            letter-spacing: -1px;
            margin-bottom: 8px;
        }
        
        .hero-card-export .hero-logo-main {
            font-size: 84px;
            margin-bottom: 24px;
        }
        
        .hero-logo-gov {
            color: #fff;
        }
        
        .hero-logo-brief {
            color: #f97316; /* Orange accent */
        }
        
        .hero-logo-today {
            color: #fff;
            font-size: 28px;
            font-weight: 900;
            letter-spacing: -1px;
            margin-left: 8px;
        }
        
        .hero-card-export .hero-logo-today {
            font-size: 84px;
            margin-left: 24px;
        }
        
        .hero-logo-date {
            color: #9ca3af;
            font-size: 12px;
            font-weight: 500;
            letter-spacing: 0.5px;
            display: block;
            margin-top: 5px;
        }
        
        .hero-card-export .hero-logo-date {
            font-size: 36px;
            margin-top: 15px;
        }
        
        /* Main Content */
        .hero-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            padding-top: 40px;
        }
        
        .hero-card-export .hero-content {
            padding-top: 120px;
        }
        
        /* Headline */
        .hero-headline {
            font-size: 26px;
            font-weight: 800;
            line-height: 1.2;
            text-align: center;
            color: #ffffff;
        }
        
        .hero-card-export .hero-headline {
            font-size: 78px;
            line-height: 1.15;
        }
        
        /* Callout with 30px top margin */
        .hero-callout {
            text-align: center;
            margin-top: 30px;
        }
        
        .hero-card-export .hero-callout {
            margin-top: 90px;
        }
        
        .hero-callout-text {
            font-size: 20px;
            line-height: 1.3;
            font-weight: 800;
            color: #f97316;
        }
        
        .hero-card-export .hero-callout-text {
            font-size: 60px;
            line-height: 1.3;
        }
        
        /* Footer */
        .hero-footer {
            text-align: center;
            padding-top: 20px;
            border-top: 2px solid #333333;
        }
        
        .hero-card-export .hero-footer {
            padding-top: 60px;
            border-top-width: 6px;
        }
        
        .hero-website {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 5px;
            color: #ffffff;
        }
        
        .hero-card-export .hero-website {
            font-size: 54px;
            margin-bottom: 15px;
        }
        
        .hero-tagline {
            font-size: 12px;
            color: #9ca3af;
        }
        
        .hero-card-export .hero-tagline {
            font-size: 36px;
        }
        
        /* Download Button */
        .download-btn {
            display: block;
            width: 100%;
            padding: 12px;
            background: #10b981;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }
        
        .download-btn:hover {
            background: #059669;
        }
        
        /* No stories message */
        .no-stories {
            background: white;
            padding: 60px 40px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .no-stories h2 {
            color: #6b7280;
            margin: 0 0 15px 0;
        }
        
        .no-stories p {
            color: #9ca3af;
            margin: 0;
        }
        
        /* Loading state */
        .loading {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(0,0,0,0.8);
            color: white;
            padding: 20px 40px;
            border-radius: 8px;
            font-size: 18px;
            z-index: 9999;
            display: none;
        }
        
        .loading.active {
            display: block;
        }
    </style>
</head>
<body>

<div class="loading" id="loading">Generating PNG...</div>

<div class="hero-page-container">
    <!-- Date Picker -->
    <div class="date-picker-section">
        <h2>Hero Cards Generator</h2>
        <div class="date-controls">
            <input type="date" id="hero-date-input" value="<?php echo $selected_date; ?>">
            <button class="btn-primary" onclick="loadDate()">Load Date</button>
            <button class="quick-date" onclick="quickDate(0)">Today</button>
            <button class="quick-date" onclick="quickDate(1)">Yesterday</button>
            <button class="quick-date" onclick="quickDate(7)">1 Week Ago</button>
        </div>
        <p style="margin: 15px 0 0 0; color: #6b7280; font-size: 14px;">
            Showing hero cards for <strong><?php echo $display_date; ?></strong>
        </p>
    </div>

    <?php
    // Query hero cards for selected date
    // Convert date to Ymd format to match ACF storage (20251006 instead of 2025-10-06)
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
                'key' => 'is_hero_card',
                'value' => '1',
                'compare' => '='
            )
        ),
        'orderby' => 'menu_order',
        'order' => 'ASC'
    );
    
    $hero_stories = new WP_Query($args);
    
    if ($hero_stories->have_posts()) :
    ?>
    
    <div class="hero-cards-grid">
        <?php 
        $card_count = 0;
        while ($hero_stories->have_posts()) : $hero_stories->the_post();
            $card_count++;
            
            // Try hero-specific headline first, fall back to regular title
            $headline = get_field('hero_headline');
            if(empty($headline)) {
                $headline = get_the_title();
            }
            
            $callout = get_field('story_callout');
            $source = get_field('headline_source');
        ?>
        
        <div class="hero-card-wrapper">
            <h3>Hero Card #<?php echo $card_count; ?></h3>
            
            <!-- Preview Card -->
            <div class="hero-card" id="hero-card-<?php echo get_the_ID(); ?>">
                <div class="hero-logo">
                    <div class="hero-logo-main">
                        <span class="hero-logo-gov">GOV</span><span class="hero-logo-brief">BRIEF</span><span class="hero-logo-today">TODAY</span>
                    </div>
                    <span class="hero-logo-date"><?php echo $display_date; ?></span>
                </div>
                
                <div class="hero-content">
                    <div class="hero-headline">
                        <?php echo esc_html($headline); ?>
                    </div>
                    
                    <?php if ($callout) : ?>
                    <div class="hero-callout">
                        <div class="hero-callout-text"><?php echo esc_html($callout); ?></div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="hero-footer">
                    <div class="hero-website">GovBrief.today</div>
                    <div class="hero-tagline">Subscribe free for daily updates</div>
                </div>
            </div>
            
            <!-- Hidden full-size export card -->
            <div class="hero-card hero-card-export" id="hero-card-export-<?php echo get_the_ID(); ?>" style="display: none;">
                <div class="hero-logo">
                    <div class="hero-logo-main">
                        <span class="hero-logo-gov">GOV</span><span class="hero-logo-brief">BRIEF</span><span class="hero-logo-today">TODAY</span>
                    </div>
                    <span class="hero-logo-date"><?php echo $display_date; ?></span>
                </div>
                
                <div class="hero-content">
                    <div class="hero-headline">
                        <?php echo esc_html($headline); ?>
                    </div>
                    
                    <?php if ($callout) : ?>
                    <div class="hero-callout">
                        <div class="hero-callout-text"><?php echo esc_html($callout); ?></div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="hero-footer">
                    <div class="hero-website">GovBrief.today</div>
                    <div class="hero-tagline">Subscribe free for daily updates</div>
                </div>
            </div>
            
            <button class="download-btn" onclick="downloadHeroCard(<?php echo get_the_ID(); ?>, '<?php echo sanitize_title($headline); ?>')">
                ⬇️ Download PNG (1080x1920)
            </button>
        </div>
        
        <?php endwhile; ?>
    </div>
    
    <?php else : ?>
    
    <div class="no-stories">
        <h2>No Hero Cards Found</h2>
        <p>No stories marked as "Hero Card" for <?php echo $display_date; ?>.</p>
        <p style="margin-top: 10px;">Check the "Hero Card" box when editing daily-headlines posts to feature them here.</p>
    </div>
    
    <?php endif; wp_reset_postdata(); ?>
    
</div>

<script>
// Date navigation
function loadDate() {
    const dateInput = document.getElementById('hero-date-input');
    window.location.href = '<?php echo get_permalink(); ?>?date=' + dateInput.value;
}

function quickDate(daysAgo) {
    const date = new Date();
    date.setDate(date.getDate() - daysAgo);
    const dateStr = date.toISOString().split('T')[0];
    window.location.href = '<?php echo get_permalink(); ?>?date=' + dateStr;
}

// Download hero card as PNG
async function downloadHeroCard(postId, filename) {
    const loading = document.getElementById('loading');
    loading.classList.add('active');
    
    try {
        // Get the full-size export version
        const exportCard = document.getElementById('hero-card-export-' + postId);
        
        // Temporarily show it for rendering
        exportCard.style.display = 'flex';
        
        // Wait a moment for fonts/styles to load
        await new Promise(resolve => setTimeout(resolve, 100));
        
        // Capture as canvas
        const canvas = await html2canvas(exportCard, {
            backgroundColor: null,
            scale: 1, // Already at full 1080x1920
            logging: false,
            useCORS: true
        });
        
        // Hide it again
        exportCard.style.display = 'none';
        
        // Convert to blob and download
        canvas.toBlob(function(blob) {
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.download = 'hero-card-' + filename + '.png';
            link.href = url;
            link.click();
            URL.revokeObjectURL(url);
            
            loading.classList.remove('active');
        }, 'image/png');
        
    } catch (error) {
        console.error('Error generating PNG:', error);
        alert('Error generating PNG. Please try again.');
        loading.classList.remove('active');
    }
}
</script>

</body>
</html>

<?php get_footer(); ?>
