<?php
/**
 * Plugin Name: ACF Product Category Cards
 * Description: A custom ACF block plugin for product category cards with background images, overlays, and buttons.
 * Version: 2.2
 * Author: Vanessa K
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// ---------------------------------------------------------------------------
// 1. Dependency notice
// ---------------------------------------------------------------------------

add_action( 'admin_notices', function () {
    if ( ! function_exists( 'acf_register_block_type' ) ) {
        echo '<div class="notice notice-error"><p><strong>ACF Product Category Cards:</strong> This plugin requires Advanced Custom Fields PRO to be installed and activated.</p></div>';
    }
} );

// ---------------------------------------------------------------------------
// 2. Register a dedicated image size for card thumbnails.
//    Hard-cropped so every card gets the same aspect ratio.
//    After deploying run: wp media regenerate --image-size=product-card-thumb
// ---------------------------------------------------------------------------

add_action( 'init', function () {
    add_image_size( 'product-card-thumb', 800, 600, true );
} );

// ---------------------------------------------------------------------------
// 3. Pre-register assets so ACF can reference them by handle.
//
//    wp_register_script/style on init makes the handles available without
//    actually enqueueing anything yet.  ACF's 'script' and 'editor_style'
//    parameters below tell WordPress to only load them when the block is
//    present on the current page — equivalent to block.json viewScript.
// ---------------------------------------------------------------------------

add_action( 'init', 'pcc_register_assets' );
function pcc_register_assets(): void {
    $url     = plugin_dir_url( __FILE__ );
    $path    = plugin_dir_path( __FILE__ );

    wp_register_script(
        'pcc-card-block',
        $url . 'card-block.js',
        array(),
        filemtime( $path . 'card-block.js' ),
        true
    );

    wp_register_style(
        'pcc-editor-style',
        $url . 'editor-style.css',
        array(),
        filemtime( $path . 'editor-style.css' )
    );
}

// ---------------------------------------------------------------------------
// 4. Register blocks via acf_register_block_type() on acf/init.
//
//    ACF blocks must go through ACF's own registration function — not
//    WordPress core's register_block_type() — so that ACF's render pipeline
//    is correctly attached.  The block.json files in blocks/ remain as
//    metadata/documentation but are not used for registration.
//
//    'script'       → enqueued on frontend + editor when block is present.
//    'editor_style' → enqueued in editor only when block is present.
// ---------------------------------------------------------------------------

add_action( 'acf/init', 'pcc_register_card_block', 20 );
function pcc_register_card_block(): void {
    if ( ! function_exists( 'acf_register_block_type' ) ) {
        return;
    }

    $block_name    = 'product-category-card';
    $full_name     = 'acf/' . $block_name;
    $template_path = plugin_dir_path( __FILE__ ) . 'blocks/product-category-card/template-product-card.php';

    if ( ( function_exists( 'acf_has_block_type' ) && acf_has_block_type( $full_name ) )
        || ! file_exists( $template_path ) ) {
        return;
    }

    acf_register_block_type( array(
        'name'            => $block_name,
        'title'           => __( 'Product Category Card' ),
        'description'     => __( 'Displays a product category card with background image, overlay, and button.' ),
        'render_template' => $template_path,
        'category'        => 'formatting',
        'icon'            => 'format-image',
        'keywords'        => array( 'card', 'product', 'category' ),
        'mode'            => 'edit',
        'supports'        => array( 'align' => false, 'mode' => true ),
        'uses_context'    => array( 'productCardGrid/columns' ),
        // Only enqueued when at least one card block is on the page.
        'script'          => 'pcc-card-block',
    ) );
}

add_action( 'acf/init', 'pcc_register_grid_block', 20 );
function pcc_register_grid_block(): void {
    static $registered = false;
    if ( $registered || ! function_exists( 'acf_register_block_type' ) ) {
        return;
    }

    $block_name    = 'product-card-grid';
    $full_name     = 'acf/' . $block_name;
    $template_path = plugin_dir_path( __FILE__ ) . 'blocks/product-card-grid/template-product-grid.php';

    if ( ( function_exists( 'acf_has_block_type' ) && acf_has_block_type( $full_name ) )
        || ! file_exists( $template_path ) ) {
        $registered = true;
        return;
    }

    acf_register_block_type( array(
        'name'             => $block_name,
        'title'            => __( 'Product Card Grid' ),
        'description'      => __( 'A container block for product category cards with adjustable columns.' ),
        'render_template'  => $template_path,
        'category'         => 'layout',
        'icon'             => 'screenoptions',
        'keywords'         => array( 'grid', 'layout', 'cards' ),
        'mode'             => 'auto',
        'supports'         => array( 'align' => false, 'mode' => false, 'jsx' => true ),
        'provides_context' => array( 'productCardGrid/columns' => 'columns' ),
        // Only enqueued in the editor when this block is present.
        'editor_style'     => 'pcc-editor-style',
    ) );

    $registered = true;
}

// ---------------------------------------------------------------------------
// 5. Enqueue shared frontend + editor stylesheet.
//
//    style.css is used by both blocks, so it's registered here rather than
//    duplicated in each block.json.  enqueue_block_assets fires on the
//    frontend and in the editor, which is exactly what we need.
// ---------------------------------------------------------------------------

add_action( 'enqueue_block_assets', 'pcc_enqueue_block_styles' );
function pcc_enqueue_block_styles(): void {
    wp_enqueue_style(
        'pcc-block-style',
        plugin_dir_url( __FILE__ ) . 'style.css',
        array(),
        filemtime( plugin_dir_path( __FILE__ ) . 'style.css' )
    );
}

// ---------------------------------------------------------------------------
// 6. Preload card images in <head> — fires before any HTML renders.
//
//    Parses the page's block data to find card attachment IDs and injects
//    <link rel="preload" as="image"> hints so the browser fetches them
//    immediately, without waiting for the stylesheet or render tree.
//
//    Only runs on the front page because that's where this block lives.
// ---------------------------------------------------------------------------

add_action( 'wp_head', 'pcc_preload_card_images', 2 );
function pcc_preload_card_images(): void {
    if ( ! is_front_page() && ! is_home() ) {
        return;
    }

    $post_id = get_option( 'page_on_front' ) ?: get_option( 'page_for_posts' );
    $post    = $post_id ? get_post( $post_id ) : get_post();

    if ( ! $post || empty( $post->post_content ) ) {
        return;
    }

    $parsed_blocks = parse_blocks( $post->post_content );

    // Collect card image attachment IDs.
    $image_ids = [];
    pcc_walk_blocks( $parsed_blocks, function ( array $block ) use ( &$image_ids ): void {
        if ( $block['blockName'] !== 'acf/product-category-card' ) {
            return;
        }
        $raw_id = $block['attrs']['data']['card_background'] ?? null;
        if ( $raw_id && is_numeric( $raw_id ) ) {
            $image_ids[] = (int) $raw_id;
        }
    } );

    if ( empty( $image_ids ) ) {
        return;
    }

    // Determine column count for an accurate sizes attribute.
    $columns = 3;
    pcc_walk_blocks( $parsed_blocks, function ( array $block ) use ( &$columns ): void {
        if ( $block['blockName'] === 'acf/product-card-grid' ) {
            $c = $block['attrs']['data']['columns'] ?? null;
            if ( $c && is_numeric( $c ) ) {
                $columns = (int) $c;
            }
        }
    } );

    $sizes_attr = pcc_get_card_sizes( $columns );

    foreach ( array_unique( $image_ids ) as $image_id ) {
        $src    = wp_get_attachment_image_url( $image_id, 'product-card-thumb' );
        $srcset = wp_get_attachment_image_srcset( $image_id, 'product-card-thumb' );

        if ( ! $src ) {
            continue;
        }

        echo '<link rel="preload" as="image" fetchpriority="high" href="' . esc_url( $src ) . '"';
        if ( $srcset ) {
            echo ' imagesrcset="' . esc_attr( $srcset ) . '" imagesizes="' . esc_attr( $sizes_attr ) . '"';
        }
        echo ">\n";
    }
}

// ---------------------------------------------------------------------------
// 7. Helper: recursively walk all blocks including inner blocks.
// ---------------------------------------------------------------------------

function pcc_walk_blocks( array $blocks, callable $cb ): void {
    foreach ( $blocks as $block ) {
        $cb( $block );
        if ( ! empty( $block['innerBlocks'] ) ) {
            pcc_walk_blocks( $block['innerBlocks'], $cb );
        }
    }
}

// ---------------------------------------------------------------------------
// 8. Helper: responsive sizes attribute based on column count.
//    Used by both pcc_preload_card_images() and template-product-card.php.
// ---------------------------------------------------------------------------

function pcc_get_card_sizes( int $columns ): string {
    $columns = max( 1, min( 5, $columns ) );

    switch ( $columns ) {
        case 1: return '100vw';
        case 2: return '(max-width: 768px) 100vw, 50vw';
        case 3: return '(max-width: 768px) 100vw, (max-width: 1024px) 50vw, 34vw';
        case 4: return '(max-width: 768px) 100vw, (max-width: 1024px) 50vw, 25vw';
        case 5: return '(max-width: 768px) 100vw, (max-width: 1024px) 34vw, 20vw';
        default: return '(max-width: 768px) 100vw, 34vw';
    }
}

// ---------------------------------------------------------------------------
// 9. ACF field group: Product Category Card
// ---------------------------------------------------------------------------

add_action( 'acf/include_fields', function () {
    if ( ! function_exists( 'acf_add_local_field_group' ) ) {
        return;
    }

    acf_add_local_field_group( array(
        'key'    => 'group_686bea4ed1434',
        'title'  => 'Product Category Card Block',
        'fields' => array(
            array(
                'key'           => 'field_686bea4fa7512',
                'label'         => 'Title',
                'name'          => 'card_title',
                'type'          => 'text',
                'default_value' => '',
                'maxlength'     => '',
            ),
            array(
                'key'           => 'field_686bed9be6cd6',
                'label'         => 'Background Image',
                'name'          => 'card_background',
                'type'          => 'image',
                'return_format' => 'array',
                'library'       => 'all',
                'preview_size'  => 'medium',
            ),
            array(
                'key'           => 'field_686bedcbe6cd7',
                'label'         => 'Enable Overlay?',
                'name'          => 'enable_overlay',
                'type'          => 'true_false',
                'default_value' => 0,
                'ui'            => 1,
            ),
            array(
                'key'               => 'field_686bede4e6cd8',
                'label'             => 'Overlay Color',
                'name'              => 'overlay_color',
                'type'              => 'color_picker',
                'conditional_logic' => array( array( array(
                    'field'    => 'field_686bedcbe6cd7',
                    'operator' => '==',
                    'value'    => '1',
                ) ) ),
                'default_value'  => '',
                'enable_opacity' => 1,
                'return_format'  => 'string',
            ),
            array(
                'key'               => 'field_686bee10e6cd9',
                'label'             => 'Overlay Opacity',
                'name'              => 'overlay_opacity',
                'type'              => 'range',
                'conditional_logic' => array( array( array(
                    'field'    => 'field_686bedcbe6cd7',
                    'operator' => '==',
                    'value'    => '1',
                ) ) ),
                'default_value' => 0.4,
                'min'           => 0,
                'max'           => 1,
                'step'          => 0.05,
            ),
            array(
                'key'   => 'field_686bee37e6cda',
                'label' => 'Button Text',
                'name'  => 'button_text',
                'type'  => 'text',
            ),
            array(
                'key'   => 'field_686bee43e6cdb',
                'label' => 'Button URL',
                'name'  => 'button_link',
                'type'  => 'url',
            ),
        ),
        'location' => array( array( array(
            'param'    => 'block',
            'operator' => '==',
            'value'    => 'acf/product-category-card',
        ) ) ),
        'menu_order'      => 0,
        'position'        => 'normal',
        'label_placement' => 'left',
        'active'          => true,
    ) );
} );

// ---------------------------------------------------------------------------
// 10. ACF field group: Product Card Grid
// ---------------------------------------------------------------------------

add_action( 'acf/include_fields', function () {
    if ( ! function_exists( 'acf_add_local_field_group' ) ) {
        return;
    }

    acf_add_local_field_group( array(
        'key'    => 'group_686bfa6c13bb1',
        'title'  => 'Product Card Grid',
        'fields' => array(
            array(
                'key'           => 'field_686bfa6c3e9ad',
                'label'         => 'Columns',
                'name'          => 'columns',
                'type'          => 'number',
                'default_value' => 3,
                'min'           => 1,
                'max'           => 5,
            ),
            array(
                'key'          => 'field_686c28e23c7cd',
                'label'        => 'Anchor ID',
                'name'         => 'anchor_id',
                'type'         => 'text',
                'instructions' => 'Optional ID to use as an anchor for this block (e.g. product-cards)',
                'maxlength'    => 50,
            ),
        ),
        'location' => array( array( array(
            'param'    => 'block',
            'operator' => '==',
            'value'    => 'acf/product-card-grid',
        ) ) ),
        'menu_order'      => 0,
        'position'        => 'normal',
        'label_placement' => 'left',
        'active'          => true,
    ) );
} );