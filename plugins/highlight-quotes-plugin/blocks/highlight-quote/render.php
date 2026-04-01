<?php
// Don't render during REST API save requests to prevent JSON corruption
if (defined('REST_REQUEST') && REST_REQUEST) {
    return;
}

$text = get_field('hq_text') ?: '';
$icon = get_field('hq_icon');
$align = get_field('hq_alignment') ?: 'left';
$preset = get_field('hq_preset_color') ?: 'primary';
$shadow = get_field('hq_shadow');
$border = get_field('hq_border_style') ?: 'none';
$style_preset = get_field('hq_style_preset') ?: 'default';

$theme_colors = wp_get_global_settings(['color','palette','theme']) ?: [];
$brand_color = '#333333'; // Default fallback

foreach ($theme_colors as $color) {
    if (isset($color['slug']) && $color['slug'] === $preset && isset($color['color'])) {
        $brand_color = $color['color'];
        break;
    }
}

$classes = array_filter([
    'highlight-quote',
    'hq-align-' . esc_attr($align),
    'hq-style-' . esc_attr($style_preset),
    $shadow ? 'hq-shadow' : '',
    $border !== 'none' ? 'hq-border-' . esc_attr($border) : ''
]);
?>
<div class="<?php echo esc_attr(implode(' ', $classes)); ?>" style="--hq-brand-color: <?php echo esc_attr($brand_color); ?>;">
    <?php if ($icon && is_array($icon) && isset($icon['url'])): ?>
        <img class="hq-icon" src="<?php echo esc_url($icon['url']); ?>" alt="">
    <?php endif; ?>
    <blockquote><?php echo wp_kses_post($text); ?></blockquote>
</div>