<?php
/*
Template Name: Search Page
*/
get_header();

// Sanitize and prepare search terms
$s = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
$search_term = strtolower($s); // Convert to lowercase for comparison

// Get all product tags and categories that contain the search term
$matching_terms = array();
$taxonomies = array('product_cat', 'product_tag', 'hardware_tag', 'hardware_category', 'cad_drawing_tag', 'cad_drawing_category');

foreach ($taxonomies as $taxonomy) {
    $terms = get_terms(array(
        'taxonomy' => $taxonomy,
        'hide_empty' => true,
    ));
    
    if (!is_wp_error($terms)) {
        foreach ($terms as $term) {
            if (strpos(strtolower($term->name), $search_term) !== false) {
                $matching_terms[] = $term->term_id;
            }
        }
    }
}

// Primary search query - includes both taxonomy matches and content search
$args = array(
    'posts_per_page' => -1,
    'post_type' => array('product', 'hardware', 'cad_drawing', 'post'),
    's' => $s, // Always include content search
);

// Add taxonomy query if we have matching terms
if (!empty($matching_terms)) {
    $args['tax_query'] = array(
        'relation' => 'OR',
        array(
            'taxonomy' => 'product_cat',
            'field'    => 'term_id',
            'terms'    => $matching_terms,
            'operator' => 'IN'
        ),
        array(
            'taxonomy' => 'product_tag',
            'field'    => 'term_id',
            'terms'    => $matching_terms,
            'operator' => 'IN'
        ),
        array(
            'taxonomy' => 'hardware_tag',
            'field'    => 'term_id',
            'terms'    => $matching_terms,
            'operator' => 'IN'
        ),
        array(
            'taxonomy' => 'hardware_category',
            'field'    => 'term_id',
            'terms'    => $matching_terms,
            'operator' => 'IN'
        ),
        array(
            'taxonomy' => 'cad_drawing_tag',
            'field'    => 'term_id',
            'terms'    => $matching_terms,
            'operator' => 'IN'
        ),
        array(
            'taxonomy' => 'cad_drawing_category',
            'field'    => 'term_id',
            'terms'    => $matching_terms,
            'operator' => 'IN'
        )
    );
    $args['_meta_or_tax'] = true; // This allows posts to match either taxonomy OR content
}

// Create the query
$search_query = new WP_Query($args);

// If no results found and we have taxonomy matches, try a broader search
if (!$search_query->have_posts() && !empty($matching_terms)) {
    $tax_args = array(
        'posts_per_page' => -1,
        'post_type' => array('product', 'hardware', 'cad_drawing', 'post'), // Include posts in fallback
        'tax_query' => array(
            'relation' => 'OR',
            array(
                'taxonomy' => 'product_cat',
                'field'    => 'term_id',
                'terms'    => $matching_terms,
                'operator' => 'IN'
            ),
            array(
                'taxonomy' => 'product_tag',
                'field'    => 'term_id',
                'terms'    => $matching_terms,
                'operator' => 'IN'
            ),
            array(
                'taxonomy' => 'hardware_tag',
                'field'    => 'term_id',
                'terms'    => $matching_terms,
                'operator' => 'IN'
            ),
            array(
                'taxonomy' => 'hardware_category',
                'field'    => 'term_id',
                'terms'    => $matching_terms,
                'operator' => 'IN'
            ),
            array(
                'taxonomy' => 'cad_drawing_tag',
                'field'    => 'term_id',
                'terms'    => $matching_terms,
                'operator' => 'IN'
            ),
            array(
                'taxonomy' => 'cad_drawing_category',
                'field'    => 'term_id',
                'terms'    => $matching_terms,
                'operator' => 'IN'
            )
        )
    );
    $search_query = new WP_Query($tax_args);
}

// If still no results, try a pure content search across all post types
if (!$search_query->have_posts()) {
    $content_args = array(
        'posts_per_page' => -1,
        'post_type' => array('product', 'hardware', 'cad_drawing', 'post'),
        's' => $s,
    );
    $search_query = new WP_Query($content_args);
}
?>

<style>
    .fixed-nav .searchHeader {
        position: relative;
    }
    .search-result-image {
        margin-bottom: 15px;
    }
    .search-result-image img {
        max-width: 100%;
        height: auto;
        display: block;
    }
    .search-result-cad-details {
        margin-top: 10px;
    }
    .search-result-cad-categories,
    .search-result-cad-tags {
        margin-bottom: 5px;
        font-size: 0.9em;
    }
</style>

<div id="primary">
    <main id="main" class="site-main mt-5" role="main">
        <div class="container">
            <header class="mb-5 searchHeader" style="z-index: -1;">
                <h1 class="page-title"> 
                    <?php echo $search_query->found_posts; ?>
                    <?php _e( 'Search Results Found For', 'locale' ); ?>: "<?php echo esc_html($s); ?>"
                </h1>
            </header> 
            <div class="row">
            <?php
            if( $search_query->have_posts() ){ 
                $types = array(
                    'Products' => 'product', 
                    'Hardware' => 'hardware',
                    'CAD Drawings' =>'cad_drawing', 
                    'Recent News' =>'post'
                );

                foreach( $types as $typedisplayname => $type ){
                    $postsfound = false;
                    echo "<h2>" . esc_html($typedisplayname) . "</h2>";

                    while( $search_query->have_posts() ){ 
                        $search_query->the_post();
                        if( $type == get_post_type() ){ 
                            $postsfound = true;
                            ?>
                            
                            <div class="search-result-card mb-5 col-sm-2 col-md-6 col-lg-4 pb-3">
                                <div class="search-result-card-body">
                                    <?php if (has_post_thumbnail()): ?>
                                        <div class="search-result-image">
                                            <?php the_post_thumbnail('thumbnail'); ?>
                                        </div>
                                    <?php endif; ?>
                                    <h3 class="search-result-card-title">
                                        <a href="<?php echo esc_url(get_the_permalink()); ?>">
                                            <?php the_title(); ?>
                                        </a>
                                    </h3>
                                    <?php if($type == 'product'): 
                                        global $product;
                                        if($product && is_a($product, 'WC_Product')):
                                    ?>
                                            <div class="search-result-product-details">
                                                <?php if ($product->get_image_id()): ?>
                                                    <div class="search-result-product-image">
                                                        <?php echo $product->get_image('thumbnail'); ?>
                                                    </div>
                                                <?php endif; ?>
                                                <div class="search-result-product-price">
                                                    <?php echo $product->get_price_html(); ?>
                                                </div>
                                                <div class="search-result-product-meta">
                                                    <?php
                                                    // Get and display categories
                                                    $categories = get_the_terms($product->get_id(), 'product_cat');
                                                    if ($categories && !is_wp_error($categories)) {
                                                        echo '<div class="search-result-product-categories">';
                                                        echo '<strong>Categories:</strong> ';
                                                        $cat_links = array();
                                                        foreach ($categories as $category) {
                                                            $cat_links[] = '<a href="' . esc_url(get_term_link($category)) . '">' . esc_html($category->name) . '</a>';
                                                        }
                                                        echo implode(', ', $cat_links);
                                                        echo '</div>';
                                                    }

                                                    // Get and display tags
                                                    $tags = get_the_terms($product->get_id(), 'product_tag');
                                                    if ($tags && !is_wp_error($tags)) {
                                                        echo '<div class="search-result-product-tags">';
                                                        echo '<strong>Tags:</strong> ';
                                                        $tag_links = array();
                                                        foreach ($tags as $tag) {
                                                            $tag_links[] = '<a href="' . esc_url(get_term_link($tag)) . '">' . esc_html($tag->name) . '</a>';
                                                        }
                                                        echo implode(', ', $tag_links);
                                                        echo '</div>';
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                    <?php 
                                        endif;
                                    elseif($type == 'cad_drawing'): 
                                    ?>
                                            <div class="search-result-cad-details">
                                                <div class="search-result-cad-meta">
                                                    <?php
                                                    // Get and display CAD drawing categories
                                                    $categories = get_the_terms(get_the_ID(), 'cad_drawing_category');
                                                    if ($categories && !is_wp_error($categories)) {
                                                        echo '<div class="search-result-cad-categories">';
                                                        echo '<strong>Categories:</strong> ';
                                                        $cat_links = array();
                                                        foreach ($categories as $category) {
                                                            $cat_links[] = '<a href="' . esc_url(get_term_link($category)) . '">' . esc_html($category->name) . '</a>';
                                                        }
                                                        echo implode(', ', $cat_links);
                                                        echo '</div>';
                                                    }

                                                    // Get and display CAD drawing tags
                                                    $tags = get_the_terms(get_the_ID(), 'cad_drawing_tag');
                                                    if ($tags && !is_wp_error($tags)) {
                                                        echo '<div class="search-result-cad-tags">';
                                                        echo '<strong>Tags:</strong> ';
                                                        $tag_links = array();
                                                        foreach ($tags as $tag) {
                                                            $tag_links[] = '<a href="' . esc_url(get_term_link($tag)) . '">' . esc_html($tag->name) . '</a>';
                                                        }
                                                        echo implode(', ', $tag_links);
                                                        echo '</div>';
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                    <?php 
                                    endif; 
                                    ?>
                                </div>
                            </div>
                    <?php 
                        }
                    }
                    if(!$postsfound){
                        echo "<h4>No results found for this category</h4>";
                    }
                    $search_query->rewind_posts();
                }
            } else {
                echo "<p>No results found.</p>";
            }
            wp_reset_postdata();
            ?>
            </div><!-- Close .row -->
        </div><!-- Close .container -->
    </main>
</div>
<?php get_footer(); ?>
