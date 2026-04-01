<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_One
 * @since Twenty Twenty-One 1.0
 */

$posts = get_posts(array(
    'numberposts' => -1,
    'post_type' => 'content'
));

if($posts) {
    ?><h3>List of Content</h3><?php
    foreach ($posts as $p) {
        echo '<h2>' . get_the_title($p) . '</h2>';
        echo get_post_field('post_content', $p);
    }
}