<?php
$header_version = '';
if (get_field('header_version')) {
    $header_version = get_field('header_version', get_the_ID());
}
get_header($header_version);
$counter = 0;
?>

<?php get_header(); ?>
<main class="site-wrapper palmshield-plus">
<section id="main-slider" style="position: relative;">
    <!-- Desktop slider - hidden on mobile -->
    <div class="desktop-slider">
        <?php echo do_shortcode('[smartslider3 slider="5"]'); ?>
    </div>
    
    <!-- Mobile slider - hidden on desktop -->
    <div class="mobile-slider">
        <?php echo do_shortcode('[smartslider3 slider="248"]'); ?>
    </div>
</section>

<style>
/* Hide mobile slider on desktop */
@media (min-width: 769px) {
    .mobile-slider {
        display: none !important;
    }
}

/* Hide desktop slider on mobile */
@media (max-width: 768px) {
    .desktop-slider {
        display: none !important;
    }
}
</style>

<!-- <section class="logo-slider">
     echo do_shortcode('[smartslider3 slider="6"]'); ?>
</section> -->
    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
            <div class="post">
                <!-- <h2 id="post-<?php the_ID(); ?>"><?php the_title(); ?></h2> -->

                
                    <?php the_content(); ?>
                </div>
            </div>
    <?php endwhile;
    endif; ?>
    </div>
</main>
<?php get_footer(); ?>