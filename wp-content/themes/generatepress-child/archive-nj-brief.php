<?php
/**
 * Archive template for NJ Briefs
 */
get_header();
?>

<style>
body {
    font-family: Arial, sans-serif;
    background: #f8f9fa;
    padding: 40px 20px;
}

.archive-wrapper {
    max-width: 1200px;
    margin: 0 auto;
}

.archive-header {
    text-align: center;
    margin-bottom: 50px;
    background: white;
    padding: 40px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.archive-header h1 {
    font-size: 48px;
    margin-bottom: 15px;
    color: #1e293b;
}

.archive-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 30px;
}

.brief-card {
    background: white;
    border-radius: 8px;
    padding: 30px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: transform 0.2s, box-shadow 0.2s;
    text-decoration: none;
    color: inherit;
    display: block;
}

.brief-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 16px rgba(0,0,0,0.15);
}

.brief-badge {
    background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
    color: #78350f;
    padding: 6px 16px;
    border-radius: 15px;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.5px;
    display: inline-block;
    margin-bottom: 15px;
}

.brief-date {
    font-size: 24px;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 10px;
}

.brief-excerpt {
    color: #64748b;
    font-size: 14px;
    line-height: 1.6;
}

.pagination {
    margin-top: 50px;
    text-align: center;
}

@media (max-width: 768px) {
    .archive-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="archive-wrapper">
    <div class="archive-header">
        <img src="https://govbrief.today/wp-content/uploads/2025/09/https___substack-post-media.s3.amazonaws.com_public_images_de5c2270-092f-47b4-8d6c-1db9ab80f506_3072x1024.png" alt="GovBrief Logo" style="max-width: 400px; margin-bottom: 20px;">
        <h1>New Jersey Edition Archive</h1>
        <p style="font-size: 18px; color: #64748b;">Browse all past editions</p>
    </div>

    <?php if (have_posts()): ?>
    <div class="archive-grid">
        <?php while (have_posts()): the_post(); 
            $brief_date = get_field('brief_date');
            $display_date = date('F j, Y', strtotime($brief_date));
        ?>
        <a href="<?php the_permalink(); ?>" class="brief-card">
            <div class="brief-badge">NJ EDITION</div>
            <h2 class="brief-date"><?php echo $display_date; ?></h2>
            <?php if (has_excerpt()): ?>
            <p class="brief-excerpt"><?php the_excerpt(); ?></p>
            <?php endif; ?>
        </a>
        <?php endwhile; ?>
    </div>

    <div class="pagination">
        <?php
        the_posts_pagination(array(
            'mid_size' => 2,
            'prev_text' => '← Previous',
            'next_text' => 'Next →',
        ));
        ?>
    </div>
    <?php else: ?>
    <p style="text-align: center; padding: 60px; background: white; border-radius: 8px;">No briefs published yet.</p>
    <?php endif; ?>
</div>

<?php get_footer(); ?>