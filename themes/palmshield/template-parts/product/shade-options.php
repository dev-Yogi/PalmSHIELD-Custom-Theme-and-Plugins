<?php

/**
 * Template part for displaying shade add-on options
 */

if (!have_rows('shade_ad-ons')) {
    return;
}
?>

<div class="shade-options-wrapper">
    <?php while (have_rows('shade_ad-ons')) : the_row(); ?>

        <?php if (get_row_layout() == 'shade_roof_options'): ?>
            <div class="shade-option-section roof-options">
                <h2 class="section-title"><?php echo get_sub_field('roof_options_section_title'); ?></h2>
                <hr>
                <?php if (have_rows('roof_options')): ?>
                    <div class="options-grid">
                        <?php while (have_rows('roof_options')): the_row(); ?>
                            <div class="option-item">
                                <h4><?php echo get_sub_field('roof_option_title'); ?></h4>
                                <?php
                                $image = get_sub_field('roof_option_image');
                                if ($image): ?>
                                    <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>">
                                <?php endif; ?>
                                <div class="option-description">
                                    <?php echo get_sub_field('roof_option_description'); ?>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if (get_row_layout() == 'shade_privacy_options'): ?>
            <div class="shade-option-section privacy-options">
                <h2 class="section-title"><?php echo get_sub_field('privacy_options_section_title'); ?></h2>
                <hr>
                <?php if (have_rows('privacy_options')): ?>
                    <div class="options-grid">
                        <?php while (have_rows('privacy_options')): the_row(); ?>
                            <div class="option-item">
                                <h4><?php echo get_sub_field('privacy_option_title'); ?></h4>
                                <?php
                                $image = get_sub_field('privacy_option_image');
                                if ($image): ?>
                                    <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>">
                                <?php endif; ?>
                                <div class="option-description">
                                    <?php echo get_sub_field('privacy_option_description'); ?>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if (get_row_layout() == 'shade_canopy_options'): ?>
            <div class="shade-option-section canopy-options">
                <h2 class="section-title"><?php echo get_sub_field('privacy_canopy_section_title'); ?></h2>
                <hr>
                <div class="canopy-fabric-vendor-section">
                <div class="canopy-vender-info-section">
                    <?php echo get_sub_field('fabric_vendor_info'); ?>
                </div>
                <?php if (have_rows('canopy_options')): ?>
                    <div class="options-grid">
                        <?php while (have_rows('canopy_options')): the_row(); ?>
                            <div class="option-item">
                                <h4><?php echo get_sub_field('canopy_option_title'); ?></h4>
                                <?php
                                $image = get_sub_field('canopy_option_image');
                                if ($image): ?>
                                    <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>">
                                <?php endif; ?>
                                <div class="option-description">
                                    <?php echo get_sub_field('canopy_option_description'); ?>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            </div>
        <?php endif; ?>

    <?php endwhile; ?>
</div>

<?php
//  styles are in product-shades.css
?>
<style>
    .shade-options-wrapper {
        clear: both;
    }
    .options-grid {
    display: grid;
	grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    margin-top: 2em;
    gap: 2em;
    margin-bottom: 2em;
}
.option-item {
    max-width: 150px;
    width: 100%;
}
</style>