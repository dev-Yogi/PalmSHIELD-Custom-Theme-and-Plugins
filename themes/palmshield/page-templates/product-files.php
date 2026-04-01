<?php

/**
 * Template Name: Product CAD Files by Type
 * Template Post Type: post, page, product, products
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package WordPress
 * @subpackage PalmShield
 */
get_header();


echo '<div class="product-cad-files-wrapper container-fluid" >';

//query all woocommerce products
$args = array(
    'post_type' => 'product',
    'posts_per_page' => -1,
);

$products = new WP_Query($args);

if ($products->have_posts()) :
    $product_groups = [];

    //organize products by category
    while ($products->have_posts()) : $products->the_post();
        $product_categories = get_the_terms(get_the_ID(), 'product_cat');
        $product_name = get_the_title();
        $product_image = get_the_post_thumbnail_url(get_the_ID(), 'medium');

        //collect CAD files for this product
        $cad_files = [];
        if (have_rows('product_resources_content')):
            while (have_rows('product_resources_content')): the_row();
                if (get_row_layout() == 'add_product_resources'):
                    if (have_rows('product_cad_drawings')) :
                        while (have_rows('product_cad_drawings')) : the_row();
                            $cad_file = get_sub_field('product_cad_file');
                            if ($cad_file) :
                                $cad_files[] = array(
                                    'url' => $cad_file['url'],
                                    'caption' => $cad_file['caption'] 
                                );
                            endif;
                        endwhile;
                    endif;
                endif;
            endwhile;
        endif;


        //organize product data under its categories
        if (!empty($cad_files) && !empty($product_categories)) {
            foreach ($product_categories as $category) {
                if (!isset($product_groups[$category->name])) {
                    $product_groups[$category->name] = [
                        'term_id' => $category->term_id,
                        'products' => []
                    ];
                }
                $product_groups[$category->name]['products'][] = array(
                    'name' => $product_name,
                    'image' => $product_image,
                    'cad_files' => $cad_files
                );
            }
        }

    endwhile;

    //display products grouped by category
    foreach ($product_groups as $category => $data) {
        $thumbnail_id = get_term_meta($data['term_id'], 'thumbnail_id', true);
        
        // Create sanitized category ID by converting to lowercase and replacing spaces with hyphens
        $category_id = strtolower(str_replace(' ', '-', $category));
        
        echo '<h2 id="'. esc_attr($category_id) .'">' . esc_html($category) . '</h2>'; // Category name
        echo '<div class="cad-container" >';
        foreach ($data['products'] as $product) {
            echo '<div class="cad-product-container" >';
            echo '<h3>' . esc_html($product['name']) . '</h3>'; // Product name
            if ($product['image']) {
                echo '<div class="product-image">';
                echo '<img src="' . esc_url($product['image']) . '" alt="' . esc_attr($product['name']) . '">';
                echo '</div>';
            }
            echo '<div class="cad-group-container" >';
            foreach ($product['cad_files'] as $file) {
                $file_extension = pathinfo($file['url'], PATHINFO_EXTENSION);
                echo '<div class="cad-item">';
                if ($file_extension === 'pdf') {
                    echo '<i class="fas fa-file-pdf resource-icon" style="font-size:15px;color:#e02826;"></i> ';
                } elseif ($file_extension === 'dwg') {
                    echo '<i class="fas fa-drafting-compass resource-icon" style="font-size:15px;color:#e02826;"></i> ';
                }
                echo '<a href="' . esc_url($file['url']) . '" target="_blank" class="resource-link">' . 
                     esc_html($file['caption']) . ' .' . esc_html($file_extension) . '</a>';
                echo '</div>';
                
            }
            echo '</div>';
            echo '</div>';
        }
        echo '</div>';
    }
endif;

wp_reset_postdata();

echo '</div>';
get_footer();
