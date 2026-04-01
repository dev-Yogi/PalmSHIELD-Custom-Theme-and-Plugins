<?php
/**
 * The template for displaying Category pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package WordPress
 * @subpackage Twenty_Fourteen
 * @since Twenty Fourteen 1.0
 */

get_header(); ?>

<?php
// Get the current category
$term = get_queried_object();

// Set up the query arguments
$args = array(
    'post_type' => 'product',
    'posts_per_page' => -1, // -1 means no limit
    'tax_query' => array(
        array(
            'taxonomy' => 'category', // Product category taxonomy
            'field'    => 'slug',
            'terms'    => $term->slug, // Current category slug
        ),
    ),
);

// Execute the query
$query = new WP_Query($args);

// Check if there are posts
if ($query->have_posts()) : 
    ?>
    <div class="products-category row container">
        <?php
        // Start the loop
        while ($query->have_posts()) : $query->the_post();
            ?>
            <div class="product-item">
                <?php
                // Display product title
                the_title('<h2>', '</h2>');

                // Display product thumbnail
                if (has_post_thumbnail()) {
                    the_post_thumbnail('medium');
                }

                // Display the excerpt or content
                the_excerpt();
                ?>
            </div>
            <?php
        endwhile;
        ?>
    </div>
    <?php
    // Reset Post Data
    wp_reset_postdata();
else :
    ?>
    <p><?php _e('No products found in this category.'); ?></p>
    <?php
endif;

// Get the footer
get_footer();
