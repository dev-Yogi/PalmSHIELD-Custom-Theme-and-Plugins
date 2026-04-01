<?php

/**
 * Plugin Name:  Get Americas Gate Posts via REST API
 * Description:  Gets the latest two posts from a blog via the REST API. Blog link, title and date included.
 * Plugin URI:   https://tjschultz.com
 * Author:       TJ Schultz
 * Version:      1.0
 * Text Domain:  getpostsviarestapi
 * License:      GPL v2 or later
 * License URI:  https://www.gnu.org/licenses/gpl-2.0.txt
 *
 * @package getpostsviarestapi
 */

// Disable direct file access.
if (! defined('ABSPATH')) {
	exit;
}

/**
 * Get posts via REST API.
 */
function get_posts_via_rest()
{

	// Initialize variable.
	$allposts = '';

	// Enter the name of your blog here followed by /wp-json/wp/v2/posts and add filters like this one that limits the result to 2 posts.
	$response = wp_remote_get('https://americasgatecompany.com/wp-json/wp/v2/products/?orderby=parent&per_page=100');

	// Exit if error.
	if (is_wp_error($response)) {
		return '<div class="error">Error fetching posts: ' . esc_html($response->get_error_message()) . '</div>';
	}

	// Check if we got a valid response code
	$response_code = wp_remote_retrieve_response_code($response);
	if ($response_code !== 200) {
		return '<div class="error">Error: Received response code ' . esc_html($response_code) . '</div>';
	}

	// Get the body and decode it
	$body = wp_remote_retrieve_body($response);
	$posts = json_decode($body);

	// Check if JSON decode was successful
	if (json_last_error() !== JSON_ERROR_NONE) {
		return '<div class="error">Error decoding JSON response: ' . esc_html(json_last_error_msg()) . '</div>';
	}

	// Exit if nothing is returned.
	if (empty($posts)) {
		return '<div class="notice">No posts found.</div>';
	}

	// If there are posts.
	if (! empty($posts)) {
?>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

		<style>
			section#breadcrumbs {
				display: none;
			}
		</style>

		<script>
			jQuery(document).ready(function($) {
				$('section#breadcrumbs .container .row .col a').remove();
			});
		</script>

<?php

		// For each post.
		$c = 0;
		foreach ($posts as $post) {

			// Remove <script> tags and their contents
			$content = preg_replace('/<script\\b[^>]*>(.*?)<\\/script>/is', '', $post->content->rendered);

			// Remove gallery pagination blocks (using observed classes)
			// Remove any <div> or <span> with class containing 'pagination-links' and their contents
			$content = preg_replace('/<div[^>]*class="[^"]*pagination-links[^"]*"[^>]*>.*?<\/div>/is', '', $content);
			$content = preg_replace('/<span[^>]*class="[^"]*pagination-links[^"]*"[^>]*>.*?<\/span>/is', '', $content);
			// Remove any <a> with class containing 'bwg-a next-page-0' or 'bwg-a last-page-0'
			$content = preg_replace('/<a[^>]*class="[^"]*bwg-a[^"]*next-page-0[^"]*"[^>]*>.*?<\/a>/is', '', $content);
			$content = preg_replace('/<a[^>]*class="[^"]*bwg-a[^"]*last-page-0[^"]*"[^>]*>.*?<\/a>/is', '', $content);

			// Allow <style> tags
			$allowed_tags = wp_kses_allowed_html('post');
			$allowed_tags['style'] = array();

			$allposts .= '<div id="div' . $c . '">' . wp_kses($content, $allowed_tags) . '</div>';
			$c++;
		}

		return $allposts;
	}

	return '<div class="notice">No content to display.</div>';
}
// Register as a shortcode to be used on the site.
add_shortcode('sc_get_posts_via_rest', 'get_posts_via_rest');

// '<a href="' . esc_url( $post->link ) . '" target=\"_blank\"><h3>' . esc_html( $post->title->rendered ) . '</h3></a> <br />'
//https://americasgatecompany.com/wp-json/wp/v2/products/?orderby=parent&per_page=21
?>