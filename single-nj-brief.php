<?php
/**
 * Single NJ Brief Template
 */

get_header();

if (have_posts()) :
    while (have_posts()) : the_post();
        
        $brief_date = get_field('brief_date');
        $display_date = date('F j, Y', strtotime($brief_date));
        $intro_copy = get_field('intro_copy');
        $rally_cry = get_field('rally_cry');
        
        // Get issue number
        $selected_date = date('Y-m-d', strtotime($brief_date));
        $issue_number = govbrief_get_nj_issue_number($selected_date);
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php the_title(); ?> - <?php bloginfo('name'); ?></title>
    <?php wp_head(); ?>
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

        .nj-brief-wrapper {
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

        .nj-badge {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            color: #78350f;
            padding: 8px 20px;
            border-radius: 20px;
            font-size: 16px;
            font-weight: 700;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 8px rgba(251, 191, 36, 0.3);
            display: inline-block;
            margin-bottom: 15px;
        }

        .nj-date {
            font-size: 32px;
            color: #1e293b;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .issue-number {
            font-size: 16px;
            color: #64748b;
        }

        .featured-image-section {
            margin-bottom: 30px;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .featured-image-section img {
            width: 100%;
            height: auto;
            border-radius: 8px;
            margin-bottom: 10px;
        }

        .featured-caption {
            font-style: italic;
            color: #64748b;
            font-size: 14px;
        }

        .intro-section {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            font-size: 18px;
            line-height: 1.6;
        }

        .cards-instruction {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            border-left: 4px solid #2563eb;
            padding: 20px;
            border-radius: 4px;
            margin-bottom: 30px;
            text-align: center;
            font-size: 18px;
            font-weight: 600;
            color: #1e40af;
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
            cursor: pointer;
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

        .rally-section {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-top: 40px;
            font-size: 18px;
            line-height: 1.6;
        }
/* Remove underlines from all links - GLOBAL */
body.single-nj-brief a,
body.single-nj-brief a:link,
body.single-nj-brief a:visited {
    text-decoration: none !important;
    border-bottom: none !important;
}
/* Hide Action Center section wrapper */
section.elementor-section:has(.elementor-element-2b898c39) {
    display: none !important;
}

/* Fallback if :has() doesn't work in older browsers */
.elementor-element.elementor-element-2b898c39,
.elementor-element.elementor-element-d0267d1 {
    display: none !important;
}
/* Specifically target the cards which are now <a> tags */
a.nj-card,
a.nj-card:hover,
a.nj-card:visited {
    text-decoration: none !important;
    border-bottom: none !important;
}

/* And the headline links inside */
.card-headline a,
.card-headline a:link,
.card-headline a:visited,
.card-headline a:hover {
    text-decoration: none !important;
    border-bottom: none !important;
}
        @media (max-width: 968px) {
            .nj-stories-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body <?php body_class(); ?>>

<div class="nj-brief-wrapper">
    <div class="nj-header">
        <img src="https://govbrief.today/wp-content/uploads/2025/09/https___substack-post-media.s3.amazonaws.com_public_images_de5c2270-092f-47b4-8d6c-1db9ab80f506_3072x1024.png" alt="GovBrief Logo" class="header-logo">
        <div class="nj-badge">NEW JERSEY EDITION</div>
        <h1 class="nj-date"><?php echo $display_date; ?></h1>
        <?php if ($issue_number): ?>
        <p class="issue-number">Issue #<?php echo $issue_number; ?></p>
        <?php endif; ?>
    </div>

    <?php if (has_post_thumbnail()): ?>
    <div class="featured-image-section">
        <?php the_post_thumbnail('large'); ?>
        <?php if (get_field('featured_caption')): ?>
        <p class="featured-caption"><?php echo esc_html(get_field('featured_caption')); ?></p>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <?php if ($intro_copy): ?>
    <div class="intro-section">
        <?php echo $intro_copy; ?>
    </div>
    <?php endif; ?>

    <div class="cards-instruction">
        👇 Click any headline or card to read the full story
    </div>

    <?php
    // Query stories for this date
    $args = array(
        'post_type' => 'daily-headlines',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'meta_query' => array(
            'relation' => 'AND',
            array(
                'key' => 'headline_date',
                'value' => $brief_date,
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

    if ($headlines->have_posts()) {
        while ($headlines->have_posts()) {
            $headlines->the_post();
            
            $primary_category = get_post_meta(get_the_ID(), '_yoast_wpseo_primary_category', true);
            $category = get_category($primary_category);
            $category_name = $category ? $category->name : 'Uncategorized';
            
            if (!isset($stories_by_category[$category_name])) {
                $stories_by_category[$category_name] = array();
            }
            
            $stories_by_category[$category_name][] = array(
                'title' => get_the_title(),
                'source' => get_field('headline_source'),
                'link' => get_field('headline_link'),
                'callout' => get_field('story_callout')
            );
        }
        wp_reset_postdata();
    }

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

    if (!empty($stories_by_category)):
    ?>
    <div class="nj-stories-grid">
        <?php 
        $story_counter = 1;
        $category_order = array('Extremism', 'Dissent', 'Disaster Relief', 'Foreign Relations', 'War', 'Health', 'Human Rights', 'Environment', 'Science', 'DEI', 'Voting Rights', 'Censorship', 'Economy', 'Military', 'Intelligence', 'Courts', 'Criminal Justice', 'Social Security', 'Immigration', 'Education', 'Oversight', 'Congress', 'Federal Personnel', 'Transportation', 'Data', 'Propaganda', 'Religion', 'Media', 'Arts', 'Grift', 'Protest', 'Fighting Back');
        
        foreach ($category_order as $cat_name) {
            if (!isset($stories_by_category[$cat_name])) continue;
            
            $color = isset($category_colors[$cat_name]) ? $category_colors[$cat_name] : '#6b7280';
            
            foreach ($stories_by_category[$cat_name] as $story) {
                ?>
                <a href="<?php echo esc_url($story['link']); ?>" target="_blank" rel="noopener" class="nj-card">
                    <div class="card-header" style="background-color: <?php echo $color; ?>;">
                        <span class="category-name"><?php echo $cat_name; ?></span>
                        <span class="story-number">#<?php echo $story_counter; ?></span>
                    </div>
                    
                    <div class="card-body">
                        <h3 class="card-headline">
                            <?php echo esc_html($story['title']); ?>
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
                </a>
                <?php
                $story_counter++;
            }
        }
        ?>
    </div>
    <?php endif; ?>

    <?php if ($rally_cry): ?>
    <div class="rally-section">
        <?php echo $rally_cry; ?>
    </div>
    <?php endif; ?>

</div>

<?php wp_footer(); ?>
</body>
</html>

<?php
    endwhile;
endif;
?>