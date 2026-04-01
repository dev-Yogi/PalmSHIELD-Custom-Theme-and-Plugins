<?php
/**
 * ACF Field Group Registration for Product Infill Options Block
 * Version 3.0 - Two-tier tab system (Categories → Orientations)
 */

add_action('acf/include_fields', function() {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    acf_add_local_field_group(array(
        'key' => 'group_product_infill_options',
        'title' => 'Product Infill Options Block',
        'fields' => array(
            // ============================================
            // GLOBAL SETTINGS
            // ============================================
            array(
                'key' => 'field_global_settings_tab',
                'label' => 'Global Settings',
                'name' => '',
                'type' => 'tab',
                'placement' => 'top',
            ),
            array(
                'key' => 'field_grid_columns',
                'label' => 'Grid Columns',
                'name' => 'grid_columns',
                'type' => 'select',
                'instructions' => 'Number of product columns in the grid',
                'choices' => array(
                    2 => '2 Columns',
                    3 => '3 Columns',
                    4 => '4 Columns',
                    5 => '5 Columns',
                ),
                'default_value' => 4,
            ),
            array(
                'key' => 'field_grid_gap',
                'label' => 'Grid Gap',
                'name' => 'grid_gap',
                'type' => 'select',
                'choices' => array(
                    'small' => 'Small',
                    'medium' => 'Medium',
                    'large' => 'Large',
                ),
                'default_value' => 'medium',
            ),
            array(
                'key' => 'field_primary_color',
                'label' => 'Primary Tab Color (Active)',
                'name' => 'primary_color',
                'type' => 'color_picker',
                'instructions' => 'Color for active tabs',
                'default_value' => '#C41E3A',
            ),
            array(
                'key' => 'field_header_bg_color',
                'label' => 'Header Background Color',
                'name' => 'header_bg_color',
                'type' => 'color_picker',
                'instructions' => 'Background color for the section header area',
                'default_value' => '#2c2c54',
            ),

            // ============================================
            // PRODUCT CATEGORIES (Primary Tabs)
            // ============================================
            array(
                'key' => 'field_categories_tab',
                'label' => 'Product Categories',
                'name' => '',
                'type' => 'tab',
                'placement' => 'top',
            ),
            array(
                'key' => 'field_product_categories',
                'label' => 'Product Categories',
                'name' => 'product_categories',
                'type' => 'repeater',
                'layout' => 'block',
                'instructions' => 'Add product categories (e.g., Louvered, Semi-Private, Solid). These appear as the PRIMARY tab row.',
                'button_label' => 'Add Category',
                'sub_fields' => array(
                    // Category Label
                    array(
                        'key' => 'field_category_label',
                        'label' => 'Category Name',
                        'name' => 'category_label',
                        'type' => 'text',
                        'required' => 1,
                        'instructions' => 'e.g., "Louvered", "Semi-Private", "Solid"',
                        'wrapper' => array('width' => '50'),
                    ),
                    // Show Category Toggle
                    array(
                        'key' => 'field_show_category',
                        'label' => 'Show Category',
                        'name' => 'show_category',
                        'type' => 'true_false',
                        'default_value' => 1,
                        'ui' => 1,
                        'wrapper' => array('width' => '25'),
                    ),
                    // Category Icon (optional)
                    array(
                        'key' => 'field_category_icon',
                        'label' => 'Category Icon',
                        'name' => 'category_icon',
                        'type' => 'image',
                        'return_format' => 'array',
                        'preview_size' => 'thumbnail',
                        'instructions' => 'Optional icon for the category tab',
                        'wrapper' => array('width' => '25'),
                    ),
                    // Category Description (shows in header when category is active)
                    array(
                        'key' => 'field_category_description',
                        'label' => 'Category Description',
                        'name' => 'category_description',
                        'type' => 'wysiwyg',
                        'toolbar' => 'basic',
                        'media_upload' => 0,
                        'instructions' => 'Description text that appears below the category title when this category is selected',
                    ),

                    // ============================================
                    // ORIENTATION TABS (Secondary Tabs within Category)
                    // ============================================
                    array(
                        'key' => 'field_orientation_tabs',
                        'label' => 'Orientation Tabs',
                        'name' => 'orientation_tabs',
                        'type' => 'repeater',
                        'layout' => 'block',
                        'instructions' => 'Add orientation tabs (e.g., Horizontal, Vertical, Diagonal). These appear as SECONDARY tabs within each category.',
                        'button_label' => 'Add Orientation Tab',
                        'sub_fields' => array(
                            array(
                                'key' => 'field_orientation_label',
                                'label' => 'Tab Label',
                                'name' => 'orientation_label',
                                'type' => 'text',
                                'required' => 1,
                                'instructions' => 'e.g., "Horizontal", "Vertical", "Diagonal"',
                                'wrapper' => array('width' => '40'),
                            ),
                            array(
                                'key' => 'field_show_orientation',
                                'label' => 'Show Tab',
                                'name' => 'show_orientation',
                                'type' => 'true_false',
                                'default_value' => 1,
                                'ui' => 1,
                                'wrapper' => array('width' => '20'),
                            ),
                            array(
                                'key' => 'field_orientation_description',
                                'label' => 'Tab Description',
                                'name' => 'orientation_description',
                                'type' => 'textarea',
                                'rows' => 2,
                                'instructions' => 'Optional footer text (e.g., "Louvered Horizontal Plank/Panel Options")',
                                'wrapper' => array('width' => '40'),
                            ),
                            // Infill Options within each orientation
                            array(
                                'key' => 'field_infill_options',
                                'label' => 'Infill Options',
                                'name' => 'infill_options',
                                'type' => 'repeater',
                                'layout' => 'table',
                                'instructions' => 'Add product options (images, captions, links, files)',
                                'button_label' => 'Add Product',
                                'sub_fields' => array(
                                    array(
                                        'key' => 'field_infill_image',
                                        'label' => 'Image',
                                        'name' => 'infill_image',
                                        'type' => 'image',
                                        'return_format' => 'array',
                                        'preview_size' => 'thumbnail',
                                    ),
                                    array(
                                        'key' => 'field_infill_caption',
                                        'label' => 'Caption',
                                        'name' => 'infill_caption',
                                        'type' => 'text',
                                    ),
                                    array(
                                        'key' => 'field_infill_link',
                                        'label' => 'Link URL',
                                        'name' => 'infill_link',
                                        'type' => 'url',
                                    ),
                                    array(
                                        'key' => 'field_infill_file_resources',
                                        'label' => 'File Resources',
                                        'name' => 'infill_file_resources',
                                        'type' => 'repeater',
                                        'layout' => 'table',
                                        'button_label' => 'Add File',
                                        'sub_fields' => array(
                                            array(
                                                'key' => 'field_infill_file',
                                                'label' => 'File',
                                                'name' => 'infill_file',
                                                'type' => 'file',
                                                'return_format' => 'array',
                                            ),
                                            array(
                                                'key' => 'field_file_label',
                                                'label' => 'Button Label',
                                                'name' => 'file_label',
                                                'type' => 'text',
                                                'instructions' => 'Optional custom label (default: file type)',
                                            ),
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    ),

                    // ============================================
                    // COLOR GALLERIES (Per Category)
                    // ============================================
                    array(
                        'key' => 'field_category_color_galleries',
                        'label' => 'Color Galleries (Creates automatic "Colors" tab)',
                        'name' => 'color_galleries',
                        'type' => 'repeater',
                        'layout' => 'block',
                        'instructions' => 'Add color galleries for this category. Creates an automatic "Colors" tab.',
                        'button_label' => 'Add Color Gallery',
                        'sub_fields' => array(
                            array(
                                'key' => 'field_gallery_title',
                                'label' => 'Gallery Title',
                                'name' => 'gallery_title',
                                'type' => 'text',
                                'instructions' => 'e.g., "Aluminum Colors", "Vinyl Colors"',
                            ),
                            array(
                                'key' => 'field_gallery_images',
                                'label' => 'Color Swatches',
                                'name' => 'gallery_images',
                                'type' => 'gallery',
                                'instructions' => 'Upload color swatch images. Use image captions for color names.',
                                'return_format' => 'array',
                                'preview_size' => 'thumbnail',
                            ),
                        ),
                    ),
                ),
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'block',
                    'operator' => '==',
                    'value' => 'acf/product-infill-options',
                ),
            ),
        ),
    ));
});
