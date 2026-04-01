<?php
// Get current product tags
$product_tags = wp_get_post_terms(get_the_ID(), 'product_tag', array('fields' => 'ids'));

// Debugging
error_log('Product Tags: ' . print_r($product_tags, true));

// If product has no tags, exit
if (empty($product_tags)) {
    error_log('No product tags found');
    return;
}

// Query hardware posts that share tags with the current product
$args = array(
    'post_type' => 'hardware',
    'posts_per_page' => 4,
    'tax_query' => array(
        'relation' => 'OR',
        array(
            'taxonomy' => 'product_tag',
            'field' => 'term_id',
            'terms' => $product_tags,
            'operator' => 'IN'
        ),
        array(
            'taxonomy' => 'post_tag',
            'field' => 'term_id',
            'terms' => $product_tags,
            'operator' => 'IN'
        )
    ),
);


$hardware_query = new WP_Query($args);

// Only display section if matching hardware is found
if ($hardware_query->have_posts()) : ?>
    <section class="related-hardware">
        <h2>Product Hardware Options</h2>
        <div class="hardware-grid">
            <?php while ($hardware_query->have_posts()) : $hardware_query->the_post(); 
                // Debug each found post
                error_log('Found hardware post: ' . get_the_title() . ' (ID: ' . get_the_ID() . ')');
            ?>
                <div class="hardware-item">
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="hardware-image">
                            <?php the_post_thumbnail('medium'); ?>
                        </div>
                    <?php endif; ?>
                    <h3 class="hardware-title">
                        <a href="<?php the_permalink(); ?>">
                            <?php the_title(); ?>
                        </a>
                    </h3>
                </div>
            <?php endwhile; ?>
        </div>
    </section>
<?php else: ?>
    <p class="no-hardware-resource-files" style="visibility: hidden; clear: both;">No hardware resource files found</p>
<?php
endif;
wp_reset_postdata();
?>

<style>
.related-hardware {
    padding: 2em 0;
    clear: both;
}

.hardware-grid {
    display: grid;
	grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    margin-top: 2em;
    gap: 2em;
    margin-bottom: 2em;
}

.hardware-item {
    text-align: center;
    align-self: baseline;
}

.hardware-image img {
    max-width: 100%;
    margin: 0 auto;
    max-height: 200px;
    object-fit: contain;   
}

/* Override CloudFlare lazy loading for hardware images */
.hardware-image img[data-cfsrc] {
    display: block !important;
    visibility: visible !important;
}

.hardware-image img[style*="display:none"] {
    display: block !important;
}

.hardware-image img[style*="visibility:hidden"] {
    visibility: visible !important;
}

.hardware-title {
    margin-top: 1em;
    font-size: 1.1em;
}

.hardware-title a {
    text-decoration: none;
    color: inherit;
}
.no-hardware-resource-files {
 clear: both;
}

@media (max-width: 768px) {
    .hardware-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    .hardware-image img {
    max-height: 150px;

}
}


</style>
