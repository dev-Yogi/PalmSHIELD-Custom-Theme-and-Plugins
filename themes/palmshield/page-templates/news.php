<?php
/**
 * Template Name: News Blog Template
 * Template Post Type: page
 */

get_header(); ?>

<div class="container main-news-container">
    <div class="row">
        <div class="col-md-12">
            <h1>PalmShield News</h1>
        </div>
        <?php
        // Set up WP Query for blog posts
        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
        $args = array(
            'post_type' => 'post',
            'posts_per_page' => 12,
            'paged' => $paged
        );
        $blog_query = new WP_Query($args);

        if ($blog_query->have_posts()) :
            while ($blog_query->have_posts()) : $blog_query->the_post();
        ?>
            <div class="col-md-3 mb-4">
                <div class="card h-100">
                    <?php if (has_post_thumbnail()) : ?>
                        <a href="<?php the_permalink(); ?>">
                            <?php the_post_thumbnail('medium', array('class' => 'card-img-top')); ?>
                        </a>
                    <?php endif; ?>
                    
                    <div class="card-body">
                        <div class="post-date mb-2">
                            <?php echo get_the_date(); ?>
                        </div>
                        <h5 class="card-title">
                            <?php the_title(); ?>
                        </h5>
                        <p class="card-text">
                            <?php 
                            $content = get_the_content();
                            $first_sentence = preg_match('/^([^.!?]*[.!?]+)/', strip_tags($content), $matches);
                            echo $matches[0] ?? wp_trim_words(get_the_excerpt(), 20);
                            ?>
                        </p>
                        <a href="<?php the_permalink(); ?>" class="btn btn-primary">Read More</a>
                    </div>
                </div>
            </div>
        <?php 
            endwhile;
        ?>
    </div>

    <div class="pagination-wrapper">
        <?php
            $big = 999999999;
            echo paginate_links(array(
                'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
                'format' => '?paged=%#%',
                'current' => max(1, get_query_var('paged')),
                'total' => $blog_query->max_num_pages,
                'prev_text' => '&laquo; Previous',
                'next_text' => 'Next &raquo;'
            ));
        ?>
    </div>
    <?php
        wp_reset_postdata();
        endif;
    ?>
</div>

<?php get_footer(); ?>

<style>

.card {
    transition: all 0.3s ease;
}

.card:hover {
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.card-img-top {
    width: 100%;
    height: 200px;
    object-fit: cover;
}

.post-date {
    color: #666;
    font-size: 0.9em;
}

.pagination-wrapper {
    text-align: center;
    margin: 2em 0;
}

.pagination-wrapper .page-numbers {
    padding: 5px 10px;
    margin: 0 5px;
    border: 1px solid #ddd;
    text-decoration: none;
}

.pagination-wrapper .current {
    background-color: #007bff;
    color: white;
}
</style>