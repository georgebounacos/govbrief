<?php
/**
 * Template Name: Hero Cards Generator - DCAT Landscape
 */

// Get date from query parameter or default to today
$selected_date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$date_obj = DateTime::createFromFormat('Y-m-d', $selected_date);
$date_obj->modify('+1 day');
$display_date = $date_obj->format('F j, Y');

get_header();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DCAT Hero Cards (Landscape) - <?php echo $display_date; ?></title>

    <!-- html2canvas for PNG export -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    <style>
        /* DCAT Brand Colors */
        :root {
            --dcat-blue: #14397d;
            --dcat-blue-light: #1e4a9a;
            --dcat-blue-accent: #5b7fc2;
            --dcat-white: #f8f9fa;
            --dcat-gray: #6b7280;
        }

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
            color: var(--dcat-blue);
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
            background: var(--dcat-blue);
            color: white;
        }

        .btn-primary:hover {
            background: var(--dcat-blue-light);
        }

        .quick-date {
            background: #f3f4f6;
            color: #374151;
        }

        .quick-date:hover {
            background: #e5e7eb;
        }

        /* Hero Cards Grid - wider for landscape */
        .hero-cards-grid {
            display: flex;
            flex-direction: column;
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

        /* The actual hero card - LANDSCAPE 1.91:1 ratio - DCAT BRANDING */
        .hero-card {
            width: 600px;
            height: 315px;
            background: var(--dcat-white);
            border-radius: 12px;
            padding: 25px 30px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            color: var(--dcat-blue);
            position: relative;
            overflow: hidden;
            margin: 0 auto 15px auto;
            box-shadow: 0 4px 20px rgba(20, 57, 125, 0.15);
        }

        /* Full size for export (1200x630) */
        .hero-card-export {
            width: 1200px;
            height: 630px;
            padding: 50px 60px;
            border-radius: 0;
            box-shadow: none;
            overflow: visible;
        }

        /* DCAT Header - horizontal layout for landscape */
        .hero-header {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .hero-card-export .hero-header {
            gap: 24px;
        }

        .dcat-logo-mark {
            width: 36px;
            height: 36px;
        }

        .hero-card-export .dcat-logo-mark {
            width: 72px;
            height: 72px;
        }

        .hero-logo-text {
            font-size: 18px;
            font-weight: 800;
            color: var(--dcat-blue);
            letter-spacing: 1px;
        }

        .hero-card-export .hero-logo-text {
            font-size: 36px;
        }

        /* Main Content */
        .hero-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 10px 0;
        }

        .hero-card-export .hero-content {
            padding: 20px 0;
        }

        /* Headline - Inverted (white on DCAT blue) */
        .hero-headline {
            font-size: 20px;
            font-weight: 800;
            line-height: 1.2;
            text-align: left;
            color: white;
            background: var(--dcat-blue);
            padding: 10px 14px;
            border-radius: 6px;
        }

        .hero-card-export .hero-headline {
            font-size: 38px;
            line-height: 1.15;
            padding: 20px 28px;
            border-radius: 10px;
        }

        /* Callout - Inverted accent (white on lighter blue) */
        .hero-callout {
            margin-top: 10px;
        }

        .hero-card-export .hero-callout {
            margin-top: 16px;
        }

        .hero-callout-text {
            font-size: 12px;
            line-height: 1.2;
            font-weight: 700;
            color: white;
            background: var(--dcat-blue-accent);
            padding: 6px 10px;
            border-radius: 4px;
            display: inline-block;
        }

        .hero-card-export .hero-callout-text {
            font-size: 22px;
            line-height: 1.2;
            padding: 12px 18px;
            border-radius: 6px;
        }

        /* Footer - stacked source and powered by */
        .hero-footer {
            padding-top: 12px;
            border-top: 2px solid var(--dcat-blue);
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .hero-card-export .hero-footer {
            padding-top: 24px;
            border-top-width: 4px;
            gap: 4px;
        }

        .hero-source-text {
            font-size: 11px;
            font-weight: 500;
            color: var(--dcat-gray);
        }

        .hero-card-export .hero-source-text {
            font-size: 22px;
        }

        .hero-powered-by {
            font-size: 11px;
            color: var(--dcat-gray);
        }

        .hero-card-export .hero-powered-by {
            font-size: 22px;
        }

        /* Download Button */
        .download-btn {
            display: inline-block;
            padding: 12px 24px;
            background: var(--dcat-blue);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }

        .download-btn:hover {
            background: var(--dcat-blue-light);
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
            background: rgba(20, 57, 125, 0.9);
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
        <h2>DCAT Top Topics Generator (Landscape)</h2>
        <div class="date-controls">
            <input type="date" id="hero-date-input" value="<?php echo $selected_date; ?>">
            <button class="btn-primary" onclick="loadDate()">Load Date</button>
            <button class="quick-date" onclick="quickDate(0)">Today</button>
            <button class="quick-date" onclick="quickDate(1)">Yesterday</button>
            <button class="quick-date" onclick="quickDate(7)">1 Week Ago</button>
        </div>
        <p style="margin: 15px 0 0 0; color: #6b7280; font-size: 14px;">
            Showing DCAT-branded hero cards (landscape format) for <strong><?php echo $display_date; ?></strong>
        </p>
    </div>

    <?php
    // Query hero cards for selected date
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

            $headline = get_field('hero_headline');
            if(empty($headline)) {
                $headline = get_the_title();
            }

            $callout = get_field('story_callout');
            $source = get_field('headline_source');
        ?>

        <div class="hero-card-wrapper">
            <h3>DCAT Top Topic #<?php echo $card_count; ?></h3>

            <!-- Preview Card -->
            <div class="hero-card" id="hero-card-<?php echo get_the_ID(); ?>">
                <div class="hero-header">
                    <!-- DCAT D-Logo SVG -->
                    <svg class="dcat-logo-mark" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10 10 L10 90 L50 90 C72 90 90 72 90 50 C90 28 72 10 50 10 L10 10 Z" fill="#14397d"/>
                        <!-- Donkey silhouette simplified -->
                        <ellipse cx="35" cy="65" rx="18" ry="12" fill="white"/>
                        <circle cx="35" cy="55" r="8" fill="white"/>
                        <path d="M30 48 L28 38 M40 48 L42 38" stroke="white" stroke-width="3" stroke-linecap="round"/>
                        <!-- Stars -->
                        <circle cx="22" cy="65" r="2" fill="#14397d"/>
                        <circle cx="30" cy="65" r="2" fill="#14397d"/>
                        <circle cx="38" cy="65" r="2" fill="#14397d"/>
                        <circle cx="46" cy="65" r="2" fill="#14397d"/>
                    </svg>
                    <span class="hero-logo-text">TOP TOPIC</span>
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
                    <?php if ($source) : ?>
                    <div class="hero-source-text">Source: <?php echo esc_html($source); ?></div>
                    <?php endif; ?>
                    <div class="hero-powered-by">Powered by GovBrief.today</div>
                </div>
            </div>

            <!-- Hidden full-size export card -->
            <div class="hero-card hero-card-export" id="hero-card-export-<?php echo get_the_ID(); ?>" style="display: none;">
                <div class="hero-header">
                    <!-- DCAT D-Logo SVG - Export Size -->
                    <svg class="dcat-logo-mark" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10 10 L10 90 L50 90 C72 90 90 72 90 50 C90 28 72 10 50 10 L10 10 Z" fill="#14397d"/>
                        <ellipse cx="35" cy="65" rx="18" ry="12" fill="white"/>
                        <circle cx="35" cy="55" r="8" fill="white"/>
                        <path d="M30 48 L28 38 M40 48 L42 38" stroke="white" stroke-width="3" stroke-linecap="round"/>
                        <circle cx="22" cy="65" r="2" fill="#14397d"/>
                        <circle cx="30" cy="65" r="2" fill="#14397d"/>
                        <circle cx="38" cy="65" r="2" fill="#14397d"/>
                        <circle cx="46" cy="65" r="2" fill="#14397d"/>
                    </svg>
                    <span class="hero-logo-text">TOP TOPIC</span>
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
                    <?php if ($source) : ?>
                    <div class="hero-source-text">Source: <?php echo esc_html($source); ?></div>
                    <?php endif; ?>
                    <div class="hero-powered-by">Powered by GovBrief.today</div>
                </div>
            </div>

            <button class="download-btn" onclick="downloadHeroCard(<?php echo get_the_ID(); ?>, 'dcat-landscape-<?php echo sanitize_title($headline); ?>')">
                ⬇️ Download DCAT PNG (1200x630)
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
        const exportCard = document.getElementById('hero-card-export-' + postId);

        // Temporarily show it for rendering
        exportCard.style.display = 'flex';

        // Wait for fonts/styles
        await new Promise(resolve => setTimeout(resolve, 100));

        // Capture as canvas
        const canvas = await html2canvas(exportCard, {
            backgroundColor: '#f8f9fa',
            scale: 1,
            logging: false,
            useCORS: true
        });

        // Hide it again
        exportCard.style.display = 'none';

        // Convert to blob and download
        canvas.toBlob(function(blob) {
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.download = filename + '.png';
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
