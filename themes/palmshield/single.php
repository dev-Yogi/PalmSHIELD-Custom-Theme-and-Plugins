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

<main id="post-content" role="main">
<section id="main-post-body">
	<div class="container">
	<?php

	if ( have_posts() ) {

		while ( have_posts() ) {
			 echo "<h1 class='the-title'>" . get_the_title() . "</h1>";
			the_post();

			the_content();
		}
	}

	?>
	</div>
</section>
<!--<section id="post-nav">
	<div class="container">
		<div class="row">
			<div class="col prev">
				<?php previous_post_link(); ?>
			</div>
			<div class="col next">
				<?php next_post_link(); ?>
			</div>
		</div>
	</div>
</section>-->
</main><!-- #site-content -->
<?php get_footer(); ?>