<?php
/**
 * Template Name: Product Category Card
 *
 * @var array  $block      Block settings and attributes.
 * @var string $content    Inner block HTML (unused for this block).
 * @var bool   $is_preview True when rendering in the block editor preview.
 * @var int    $post_id    The post the block belongs to.
 * @var array  $context    Context values passed from parent blocks.
 */

// ---------------------------------------------------------------------------
// Fields
// ---------------------------------------------------------------------------

$title           = get_field( 'card_title' );
$bg              = get_field( 'card_background' );
$overlay_enabled = get_field( 'enable_overlay' );
$overlay_color   = get_field( 'overlay_color' ) ?: '#000000';
$overlay_opacity = get_field( 'overlay_opacity' );

// 0 is a valid opacity value — only fall back when the field is truly empty.
if ( $overlay_opacity === '' || $overlay_opacity === null ) {
    $overlay_opacity = 0.4;
}

$button_text = get_field( 'button_text' );
$button_link = get_field( 'button_link' );

// ---------------------------------------------------------------------------
// Image loading strategy
//
// On the front page, every card is already preloaded via <link rel="preload">
// injected in wp_head (see pcc_preload_card_images in the plugin file).
// Marking them eager + high lets the browser confirm these are render-critical
// and not defer them behind other resources.
//
// On all other pages (no preload hints), lazy + auto prevents off-screen
// images from competing with actual above-fold content.
// ---------------------------------------------------------------------------

$is_front_page  = ! $is_preview && ( is_front_page() || is_home() );
$fetch_priority = $is_front_page ? 'high'  : 'auto';
$loading        = $is_front_page ? 'eager' : 'lazy';
$decoding       = $is_front_page ? 'sync'  : 'async';

// ---------------------------------------------------------------------------
// Column count — passed down from the grid block via block context.
// Drives the sizes attribute so the browser requests the right image width.
// ---------------------------------------------------------------------------

$columns = isset( $context['productCardGrid/columns'] ) ? (int) $context['productCardGrid/columns'] : 3;
$sizes   = pcc_get_card_sizes( $columns );

// ---------------------------------------------------------------------------
// Build <img> via wp_get_attachment_image() so srcset, sizes, and alt text
// come from the media library automatically.
// ---------------------------------------------------------------------------

$img_html = '';
if ( ! empty( $bg['id'] ) ) {
    $img_html = wp_get_attachment_image(
        $bg['id'],
        'product-card-thumb',
        false,
        array(
            'class'         => 'card-bg-image',
            'fetchpriority' => $fetch_priority,
            'loading'       => $loading,
            'decoding'      => $decoding,
            'sizes'         => $sizes,
            'alt'           => '',  // Decorative — context is in the card title / button.
        )
    );
}

// ---------------------------------------------------------------------------
// Wrapper classes
// ---------------------------------------------------------------------------

$wrapper_classes = 'product-card-block';
if ( ! empty( $block['className'] ) ) {
    $wrapper_classes .= ' ' . esc_attr( $block['className'] );
}
?>

<div class="<?php echo $wrapper_classes; ?>">

    <?php echo $img_html; ?>

    <?php if ( $overlay_enabled ) : ?>
        <div class="card-overlay" style="background-color: <?php echo esc_attr( $overlay_color ); ?>; opacity: <?php echo esc_attr( (float) $overlay_opacity ); ?>;"></div>
    <?php endif; ?>

    <div class="card-content">
        <?php if ( $title ) : ?>
            <h3><?php echo esc_html( $title ); ?></h3>
        <?php endif; ?>

        <?php if ( $button_link && $button_text ) : ?>
            <a href="<?php echo esc_url( $button_link ); ?>" class="card-button">
                <?php echo esc_html( $button_text ); ?>
            </a>
        <?php endif; ?>
    </div>

</div>