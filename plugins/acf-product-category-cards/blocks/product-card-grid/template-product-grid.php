<?php
/**
 * Template Name: Product Card Grid
 *
 * @var array  $block      Block settings and attributes.
 * @var string $content    Inner block HTML (populated on the frontend).
 * @var bool   $is_preview True when rendering in the block editor preview.
 * @var int    $post_id    The post the block belongs to.
 * @var array  $context    Context values passed from parent blocks.
 */

$columns   = max( 1, min( 5, (int) ( get_field( 'columns' ) ?: 3 ) ) );
$anchor_id = get_field( 'anchor_id' );

$classes = array( 'product-card-grid', 'columns-' . $columns );
if ( ! empty( $block['className'] ) ) {
    $classes[] = esc_attr( $block['className'] );
}

// The CSS custom property feeds the editor-style.css grid override as well as
// the frontend columns-N class, so both stay in sync from a single source.
$inline_style = sprintf(
    '--product-card-grid-columns: %1$d; display: grid; grid-template-columns: repeat(%1$d, 1fr); gap: 1.5rem;',
    $columns
);
?>

<div
    <?php if ( $anchor_id ) : ?>id="<?php echo esc_attr( $anchor_id ); ?>"<?php endif; ?>
    class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"
    style="<?php echo esc_attr( $inline_style ); ?>"
    data-columns="<?php echo esc_attr( $columns ); ?>"
>
    <InnerBlocks />
</div>