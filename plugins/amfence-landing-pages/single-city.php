<?php

/**
 * Template Name: Single City
 * 
 * The template for displaying location landing pages.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package WordPress
 * @subpackage amfence
 * @since 1.0.0
 */


get_header();
$contentLoader = new AMFLP_Loader('content', get_the_ID());
$cityLoader = new AMFLP_Loader('city', get_the_ID());
$controller = new AMFLP_Controller($contentLoader, $cityLoader);

$postData = $controller->theData();
$cityData = $postData['city'];
$contentData = $postData['content'];

// Output processed content
foreach ($contentData as $section => $content) {
    echo apply_filters('the_content', $content);
}

?>    
</main>
<?php
get_footer();