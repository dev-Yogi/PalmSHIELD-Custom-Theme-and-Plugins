<?php

/**
 * The template for displaying single posts and pages.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package WordPress
 * @subpackage amfence
 * @since 1.0.0
 */

get_header();
?>

<main id="page-content" role="main">
    <section id="main-body">
        <div class="container-fluid">
            <?php

            if (have_posts()) {

                while (have_posts()) {
                    the_post();

                    the_content();
                }
            }
            ?>
        </div>
    </section>
</main><!-- #site-content -->

<?php 
// Get the current site type and load the appropriate footer
$site_type = get_current_site_type();
switch ($site_type) {
    case 'acoustic':
        get_footer('acoustic');
        break;
    case 'rooftop':
        get_footer('rooftop');
        break;
    case 'shades':
        get_footer('palmshades');
        break;
    case 'elite':
        get_footer('elite');
        break;
    default:
        get_footer();
        break;
}
?> 