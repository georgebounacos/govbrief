<?php
/**
 * Template Name: Weekly Quote View (Social Media)
 * Description: Generate large shareable graphic showing quotes from a week
 */

// Get parameters
$selected_date = isset($_GET['date']) ? sanitize_text_field($_GET['date']) : date('Y-m-d');
$week_format = isset($_GET['format']) ? sanitize_text_field($_GET['format']) : 'sun-sat'; // sun-sat or mon-fri

$date_obj = DateTime::createFromFormat('Y-m-d', $selected_date);

// Calculate week range
if ($week_format === 'mon-fri') {
    $day_of_week = $date_obj->format('N'); // 1=Monday, 7=Sunday
    $days_since_monday = $day_of_week - 1;
    $week_start = clone $date_obj;
    $week_start->modify("-{$days_since_monday} days");
    $week_end = clone $week_start;
    $week_end->modify('+4 days');
    $days_to_show = 5;
} else {
    $day_of_week = $date_obj->format('w'); // 0=Sunday, 6=Saturday
    $week_start = clone $date_obj;
    $week_start->modify("-{$day_of_week} days");
    $week_end = clone $week_start;
    $week_end->modify('+6 days');
    $days_to_show = 7;
}

$week_display = $week_start->format('F j') . ' - ' . $week_end->format('F j, Y');

// Query for posts with quotes in this date range
$quote_posts = [];
$current_date = clone $week_start;

for ($i = 0; $i < $days_to_show; $i++) {
    $date_string = $current_date->format('Y-m-d');
    
    $args = [
        'post_type' => 'post',
        'posts_per_page' => 1,
        'meta_query' => [
            [
                'key' => 'calendar_date',
                'value' => $date_string,
                'compare' => '=',
                'type' => 'DATE'
            ]
        ]
    ];
    
    $query = new WP_Query($args);
    
    if ($query->have_posts()) {
        $query->the_post();
        $quote_text = get_field('gbt_quote_text');
        $quote_cite = get_field('gbt_quote_citation');
        
        if ($quote_text || $quote_cite) {
            $quote_posts[] = [
                'date' => $current_date->format('l'), // Day name
                'quote' => $quote_text,
                'citation' => $quote_cite
            ];
        }
        wp_reset_postdata();
    }
    
    $current_date->modify('+1 day');
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Quotes of the Week - <?php echo $week_display; ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Georgia, 'Times New Roman', serif;
            background: #f5f5f5;
            padding: 20px;
        }
        
        .controls {
            max-width: 1400px;
            margin: 0 auto 20px;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .controls h2 {
            margin-bottom: 15px;
            color: #007cba;
        }
        
        .date-selector {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
            margin-bottom: 15px;
        }
        
        .date-selector input,
        .date-selector select,
        .date-selector button {
            padding: 10px 15px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .date-selector button {
            background: #007cba;
            color: white;
            border: none;
            cursor: pointer;
            font-weight: 600;
        }
        
        .date-selector button:hover {
            background: #005a8a;
        }
        
        .export-btn {
            background: #16a34a;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 10px;
        }
        
        .export-btn:hover {
            background: #15803d;
        }
        
        /* QUOTE CARD - OPTIMIZED FOR SOCIAL MEDIA */
        #quote-card {
            width: 1400px;
            margin: 0 auto;
            background: #fffbf0;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .card-header {
            background: linear-gradient(135deg, #007cba 0%, #005a8a 100%);
            color: white;
            padding: 50px 60px;
            text-align: center;
        }
        
        .card-header h1 {
            font-size: 56px;
            font-weight: 900;
            letter-spacing: 2px;
            margin-bottom: 15px;
            text-transform: uppercase;
        }
        
        .card-header .subtitle {
            font-size: 32px;
            font-weight: 600;
            opacity: 0.95;
        }
        
        .card-header .date-range {
            font-size: 26px;
            margin-top: 10px;
            opacity: 0.9;
        }
        
        .quotes-container {
            padding: 50px 60px;
        }
        
        .quote-day {
            background: white;
            border-left: 8px solid #d97706;
            padding: 40px 45px;
            margin-bottom: 35px;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.08);
        }
        
        .quote-day:last-child {
            margin-bottom: 0;
        }
        
        .day-label {
            color: #d97706;
            font-size: 32px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 20px;
            font-family: Arial, sans-serif;
        }
        
        .quote-text {
            font-size: 28px;
            line-height: 1.6;
            color: #1a1a1a;
            font-style: italic;
            margin-bottom: 20px;
        }
        
        .quote-citation {
            font-size: 22px;
            color: #666;
            font-style: normal;
            line-height: 1.5;
        }
        
        .no-quotes {
            text-align: center;
            padding: 60px;
            color: #999;
            font-size: 28px;
        }
        
        @media print {
            body {
                background: white;
            }
            .controls {
                display: none;
            }
        }
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
</head>
<body>

<!-- Controls -->
<div class="controls">
    <h2>Quote Card Generator</h2>
    
    <form class="date-selector" method="get">
        <label>Select Date:</label>
        <input type="date" name="date" value="<?php echo $selected_date; ?>" max="<?php echo date('Y-m-d'); ?>">
        
        <label>Format:</label>
        <select name="format">
            <option value="sun-sat" <?php selected($week_format, 'sun-sat'); ?>>Sunday-Saturday (7 days)</option>
            <option value="mon-fri" <?php selected($week_format, 'mon-fri'); ?>>Monday-Friday (5 days)</option>
        </select>
        
        <button type="submit">Load Week</button>
    </form>
    
    <button class="export-btn" onclick="exportImage()">📥 Download as PNG</button>
    <p style="margin-top: 10px; color: #666; font-size: 14px;">
        Image will be 1400px wide - optimized for Facebook, Instagram, and Twitter/X
    </p>
</div>

<!-- Quote Card (This gets exported) -->
<div id="quote-card">
    <div class="card-header">
        <h1>GOVBRIEF.TODAY</h1>
        <div class="subtitle">QUOTES OF THE WEEK</div>
        <div class="date-range"><?php echo $week_display; ?></div>
    </div>
    
    <div class="quotes-container">
        <?php if (!empty($quote_posts)): ?>
            <?php foreach ($quote_posts as $quote): ?>
                <div class="quote-day">
                    <div class="day-label"><?php echo strtoupper($quote['date']); ?></div>
                    
                    <?php if ($quote['quote']): ?>
                        <div class="quote-text">"<?php echo esc_html($quote['quote']); ?>"</div>
                    <?php endif; ?>
                    
                    <?php if ($quote['citation']): ?>
                        <div class="quote-citation">— <?php echo esc_html($quote['citation']); ?></div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-quotes">
                No quotes found for this week.<br>
                Make sure posts have calendar_date set and quote fields filled in.
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function exportImage() {
    const button = event.target;
    const originalText = button.textContent;
    button.textContent = '⏳ Generating...';
    button.disabled = true;
    
    // Hide controls before export
    document.querySelector('.controls').style.display = 'none';
    
    html2canvas(document.getElementById('quote-card'), {
        scale: 2, // High quality
        backgroundColor: '#fffbf0',
        logging: false,
        windowWidth: 1400,
        windowHeight: document.getElementById('quote-card').scrollHeight
    }).then(canvas => {
        canvas.toBlob(blob => {
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            const dateStr = '<?php echo $week_start->format("Ymd"); ?>';
            link.download = 'govbrief-quotes-week-' + dateStr + '.png';
            link.href = url;
            link.click();
            URL.revokeObjectURL(url);
            
            // Restore controls
            document.querySelector('.controls').style.display = 'block';
            button.textContent = originalText;
            button.disabled = false;
            
            alert('✅ Quote card downloaded! Ready to share on social media.');
        }, 'image/png');
    }).catch(error => {
        console.error('Export error:', error);
        alert('Error generating image. Please try again.');
        document.querySelector('.controls').style.display = 'block';
        button.textContent = originalText;
        button.disabled = false;
    });
}

function setDate(date) {
    const urlParams = new URLSearchParams(window.location.search);
    urlParams.set('date', date);
    window.location.search = urlParams.toString();
}
</script>

</body>
</html>