<?php
/**
 * Product Infill Options Block Template.
 * Version 3.0 - Two-tier tab system (Categories → Orientations)
 */

// Create id attribute allowing for custom "anchor" value.
$block_id = $block['id'];
$id = 'product-infill-options-' . $block_id;
if (!empty($block['anchor'])) {
    $id = $block['anchor'];
}

// Create class attribute allowing for custom "className"
$className = 'product-infill-options';
if (!empty($block['className'])) {
    $className .= ' ' . $block['className'];
}

// Load global settings
$grid_columns = get_field('grid_columns') ?: 4;
$grid_gap = get_field('grid_gap') ?: 'medium';
$primary_color = get_field('primary_color') ?: '#C41E3A';
$header_bg_color = get_field('header_bg_color') ?: '#2c2c54';

// Load product categories
$product_categories = get_field('product_categories') ?: [];

// Filter to only show visible categories
$visible_categories = array_filter($product_categories, function($cat) {
    return !empty($cat['category_label']) && $cat['show_category'];
});
$visible_categories = array_values($visible_categories); // Re-index

// Build grid classes
$grid_classes = sprintf(
    'product-options-grid gap-%s columns-%d',
    esc_attr($grid_gap),
    esc_attr($grid_columns)
);

// Get block wrapper attributes
$wrapper_attributes = get_block_wrapper_attributes([
    'class' => $className
]);

// Inline CSS variables for dynamic colors
$custom_styles = sprintf(
    '--primary-tab-color: %s; --header-bg-color: %s;',
    esc_attr($primary_color),
    esc_attr($header_bg_color)
);
?>

<div <?php echo $wrapper_attributes; ?> id="<?php echo esc_attr($id); ?>" style="<?php echo esc_attr($custom_styles); ?>">

    <?php if (!empty($visible_categories)): ?>
        
        <!-- ============================================ -->
        <!-- PRIMARY CATEGORY TABS (Top Row) -->
        <!-- ============================================ -->
        <div class="category-tabs-wrapper">
            <!-- Desktop: horizontal tab buttons -->
            <div class="category-tab-buttons">
                <?php foreach ($visible_categories as $cat_index => $category): ?>
                    <button class="category-tab-button <?php echo $cat_index === 0 ? 'active' : ''; ?>"
                            data-category="category-<?php echo esc_attr($block_id); ?>-<?php echo esc_attr($cat_index); ?>">
                        <?php if (!empty($category['category_icon'])): ?>
                            <img src="<?php echo esc_url($category['category_icon']['url']); ?>" 
                                 alt="" 
                                 class="category-icon">
                        <?php endif; ?>
                        <span><?php echo esc_html($category['category_label']); ?></span>
                    </button>
                <?php endforeach; ?>
            </div>

            <!-- Mobile: dropdown selector -->
            <div class="category-tabs-mobile-dropdown" aria-hidden="true">
                <button type="button" class="category-dropdown-trigger" aria-expanded="false" aria-haspopup="listbox" id="<?php echo esc_attr($id); ?>-category-trigger">
                    <?php
                    $first_cat = $visible_categories[0];
                    if (!empty($first_cat['category_icon'])): ?>
                        <img src="<?php echo esc_url($first_cat['category_icon']['url']); ?>" alt="" class="category-icon">
                    <?php endif; ?>
                    <span class="category-dropdown-label"><?php echo esc_html($first_cat['category_label']); ?></span>
                    <svg class="category-dropdown-chevron" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </button>
                <div class="category-dropdown-menu" role="listbox" id="<?php echo esc_attr($id); ?>-category-menu" hidden>
                    <?php foreach ($visible_categories as $cat_index => $category): ?>
                        <div class="category-dropdown-option" role="option" tabindex="0" aria-selected="<?php echo $cat_index === 0 ? 'true' : 'false'; ?>"
                             data-category="category-<?php echo esc_attr($block_id); ?>-<?php echo esc_attr($cat_index); ?>">
                            <?php if (!empty($category['category_icon'])): ?>
                                <img src="<?php echo esc_url($category['category_icon']['url']); ?>" alt="" class="category-icon">
                            <?php endif; ?>
                            <span><?php echo esc_html($category['category_label']); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- ============================================ -->
        <!-- CATEGORY CONTENT PANELS -->
        <!-- ============================================ -->
        <?php foreach ($visible_categories as $cat_index => $category): 
            $orientation_tabs = is_array($category['orientation_tabs'] ?? null) ? $category['orientation_tabs'] : [];
            $color_galleries = is_array($category['color_galleries'] ?? null) ? $category['color_galleries'] : [];
            
            // Filter visible orientation tabs
            $visible_orientations = array_filter($orientation_tabs, function($tab) {
                return !empty($tab['orientation_label']) && $tab['show_orientation'];
            });
            $visible_orientations = array_values($visible_orientations);
            
            // Check if we have colors for this category
            $has_colors = !empty($color_galleries);
        ?>
            <div class="category-content <?php echo $cat_index === 0 ? 'active' : ''; ?>"
                 data-category="category-<?php echo esc_attr($block_id); ?>-<?php echo esc_attr($cat_index); ?>">
                
                <!-- Section Header -->
                <div class="infill-section-header">
                    <h2 class="infill-title">
                        <span class="category-name"><?php echo esc_html($category['category_label']); ?></span>
                        <span class="infill-label">INFILL OPTIONS</span>
                    </h2>
                    <?php if (!empty($category['category_description'])): ?>
                        <div class="infill-description">
                            <?php echo wp_kses_post($category['category_description']); ?>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if (!empty($visible_orientations) || $has_colors): ?>
                    
                    <!-- ============================================ -->
                    <!-- SECONDARY ORIENTATION TABS -->
                    <!-- ============================================ -->
                    <div class="orientation-tabs-wrapper">
                        <div class="orientation-tab-buttons">
                            <?php foreach ($visible_orientations as $orient_index => $orientation): ?>
                                <button class="orientation-tab-button <?php echo $orient_index === 0 ? 'active' : ''; ?>"
                                        data-orientation="orient-<?php echo esc_attr($block_id); ?>-<?php echo esc_attr($cat_index); ?>-<?php echo esc_attr($orient_index); ?>">
                                    <?php echo esc_html($orientation['orientation_label']); ?>
                                </button>
                            <?php endforeach; ?>
                            
                            <?php if ($has_colors): ?>
                                <button class="orientation-tab-button <?php echo empty($visible_orientations) ? 'active' : ''; ?>"
                                        data-orientation="orient-<?php echo esc_attr($block_id); ?>-<?php echo esc_attr($cat_index); ?>-colors">
                                    Colors
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- ============================================ -->
                    <!-- ORIENTATION CONTENT PANELS -->
                    <!-- ============================================ -->
                    <?php foreach ($visible_orientations as $orient_index => $orientation): ?>
                        <div class="orientation-content <?php echo $orient_index === 0 ? 'active' : ''; ?>"
                             data-orientation="orient-<?php echo esc_attr($block_id); ?>-<?php echo esc_attr($cat_index); ?>-<?php echo esc_attr($orient_index); ?>">
                            
                            <?php if (!empty($orientation['infill_options'])): ?>
                                <div class="<?php echo esc_attr($grid_classes); ?>">
                                    <?php foreach ($orientation['infill_options'] as $option): ?>
                                        <div class="product-option">
                                            <?php if (!empty($option['infill_link'])): ?>
                                                <a href="<?php echo esc_url($option['infill_link']); ?>">
                                            <?php endif; ?>

                                            <div class="product-image-wrapper">
                                                <?php if (!empty($option['infill_image'])): ?>
                                                    <img src="<?php echo esc_url($option['infill_image']['url']); ?>"
                                                         alt="<?php echo esc_attr($option['infill_image']['alt'] ?: $option['infill_caption']); ?>">
                                                <?php endif; ?>
                                                
                                                <?php if (!empty($option['infill_file_resources'])): ?>
                                                    <div class="file-badges">
                                                        <?php foreach ($option['infill_file_resources'] as $resource): ?>
                                                            <?php if (!empty($resource['infill_file'])): 
                                                                $file_ext = strtoupper(pathinfo($resource['infill_file']['filename'], PATHINFO_EXTENSION));
                                                                $label = !empty($resource['file_label']) ? $resource['file_label'] : $file_ext;
                                                            ?>
                                                                <a href="<?php echo esc_url($resource['infill_file']['url']); ?>" 
                                                                   class="file-badge"
                                                                   title="Download <?php echo esc_attr($label); ?>"
                                                                   download
                                                                   onclick="event.stopPropagation();">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                                                        <polyline points="14 2 14 8 20 8"></polyline>
                                                                        <line x1="12" y1="18" x2="12" y2="12"></line>
                                                                        <polyline points="9 15 12 18 15 15"></polyline>
                                                                    </svg>
                                                                    <span><?php echo esc_html($label); ?></span>
                                                                </a>
                                                            <?php endif; ?>
                                                        <?php endforeach; ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>

                                            <?php if (!empty($option['infill_caption'])): ?>
                                                <p class="product-caption"><?php echo esc_html($option['infill_caption']); ?></p>
                                            <?php endif; ?>

                                            <?php if (!empty($option['infill_link'])): ?>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($orientation['orientation_description'])): ?>
                                <div class="orientation-footer">
                                    <?php echo wp_kses_post($orientation['orientation_description']); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>

                    <!-- ============================================ -->
                    <!-- COLORS TAB CONTENT -->
                    <!-- ============================================ -->
                    <?php if ($has_colors): ?>
                        <div class="orientation-content <?php echo empty($visible_orientations) ? 'active' : ''; ?>"
                             data-orientation="orient-<?php echo esc_attr($block_id); ?>-<?php echo esc_attr($cat_index); ?>-colors">
                            
                            <div class="color-galleries-container">
                                <?php foreach ($color_galleries as $gallery): ?>
                                    <?php if (!empty($gallery['gallery_images'])): ?>
                                        <div class="color-gallery-group">
                                            <?php if (!empty($gallery['gallery_title'])): ?>
                                                <h3 class="color-gallery-title">
                                                    <?php echo esc_html($gallery['gallery_title']); ?>
                                                </h3>
                                            <?php endif; ?>

                                            <div class="color-gallery-grid">
                                                <?php foreach ($gallery['gallery_images'] as $image): ?>
                                                    <div class="color-swatch">
                                                        <img src="<?php echo esc_url($image['url']); ?>"
                                                             alt="<?php echo esc_attr($image['alt'] ?: $image['caption']); ?>">
                                                        <?php if (!empty($image['caption'])): ?>
                                                            <p class="color-swatch-name"><?php echo esc_html($image['caption']); ?></p>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                <?php endif; ?>
            </div>
        <?php endforeach; ?>

    <?php else: ?>
        <!-- Empty State for Editor -->
        <div class="infill-empty-state">
            <p>Add product categories to get started. Each category can have multiple orientation tabs (Horizontal, Vertical, etc.) and color galleries.</p>
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('<?php echo esc_js($id); ?>');
    if (!container) return;

    // ============================================
    // PRIMARY CATEGORY TAB SWITCHING
    // ============================================
    const categoryButtons = container.querySelectorAll('.category-tab-button');
    const categoryContents = container.querySelectorAll('.category-content');
    const mobileDropdown = container.querySelector('.category-tabs-mobile-dropdown');
    const dropdownTrigger = container.querySelector('.category-dropdown-trigger');
    const dropdownMenu = container.querySelector('.category-dropdown-menu');
    const dropdownLabel = container.querySelector('.category-dropdown-label');
    const dropdownOptions = container.querySelectorAll('.category-dropdown-option');

    function setActiveCategory(categoryId) {
        const button = container.querySelector(`.category-tab-button[data-category="${categoryId}"]`);
        if (!button) return;

        categoryButtons.forEach(btn => btn.classList.remove('active'));
        button.classList.add('active');

        categoryContents.forEach(content => content.classList.remove('active'));
        const activeCategory = container.querySelector(`.category-content[data-category="${categoryId}"]`);
        if (activeCategory) {
            activeCategory.classList.add('active');
            const orientationButtons = activeCategory.querySelectorAll('.orientation-tab-button');
            const orientationContents = activeCategory.querySelectorAll('.orientation-content');
            orientationButtons.forEach((btn, index) => btn.classList.toggle('active', index === 0));
            orientationContents.forEach((content, index) => content.classList.toggle('active', index === 0));
        }

        // Keep mobile dropdown trigger and aria-selected in sync
        if (dropdownTrigger && dropdownLabel) {
            const option = container.querySelector(`.category-dropdown-option[data-category="${categoryId}"]`);
            if (option) {
                const icon = option.querySelector('.category-icon');
                const text = option.querySelector('span');
                dropdownLabel.textContent = text ? text.textContent : '';
                const triggerIcon = dropdownTrigger.querySelector('.category-icon');
                if (triggerIcon && icon && icon.src) {
                    triggerIcon.src = icon.src;
                    triggerIcon.style.display = '';
                } else if (triggerIcon) {
                    triggerIcon.style.display = icon && icon.src ? '' : 'none';
                }
            }
        }
        dropdownOptions.forEach(opt => {
            opt.setAttribute('aria-selected', opt.dataset.category === categoryId ? 'true' : 'false');
        });
    }

    function closeCategoryDropdown() {
        if (!dropdownMenu || !dropdownTrigger) return;
        dropdownMenu.setAttribute('hidden', '');
        dropdownTrigger.setAttribute('aria-expanded', 'false');
        mobileDropdown && mobileDropdown.classList.remove('is-open');
    }

    categoryButtons.forEach(button => {
        button.addEventListener('click', () => {
            setActiveCategory(button.dataset.category);
        });
    });

    // Mobile dropdown: toggle menu
    if (dropdownTrigger && dropdownMenu) {
        dropdownTrigger.addEventListener('click', (e) => {
            e.stopPropagation();
            const isOpen = dropdownMenu.hasAttribute('hidden') === false;
            if (isOpen) {
                closeCategoryDropdown();
            } else {
                dropdownMenu.removeAttribute('hidden');
                dropdownTrigger.setAttribute('aria-expanded', 'true');
                mobileDropdown && mobileDropdown.classList.add('is-open');
            }
        });

        dropdownOptions.forEach(option => {
            option.addEventListener('click', () => {
                setActiveCategory(option.dataset.category);
                closeCategoryDropdown();
            });
            option.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    setActiveCategory(option.dataset.category);
                    closeCategoryDropdown();
                }
            });
        });

        document.addEventListener('click', (e) => {
            if (mobileDropdown && !mobileDropdown.contains(e.target)) {
                closeCategoryDropdown();
            }
        });
    }

    // ============================================
    // SECONDARY ORIENTATION TAB SWITCHING
    // ============================================
    const orientationButtons = container.querySelectorAll('.orientation-tab-button');

    orientationButtons.forEach(button => {
        button.addEventListener('click', () => {
            const orientationId = button.dataset.orientation;
            const parentCategory = button.closest('.category-content');

            if (!parentCategory) return;

            // Update orientation button states within this category only
            const siblingButtons = parentCategory.querySelectorAll('.orientation-tab-button');
            const siblingContents = parentCategory.querySelectorAll('.orientation-content');

            siblingButtons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');

            siblingContents.forEach(content => content.classList.remove('active'));
            const activeOrientation = parentCategory.querySelector(`.orientation-content[data-orientation="${orientationId}"]`);
            if (activeOrientation) {
                activeOrientation.classList.add('active');
            }
        });
    });
});
</script>
