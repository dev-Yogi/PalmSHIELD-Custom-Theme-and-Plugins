jQuery(document).ready(function($) {
    'use strict';
    
    var $form = $('form.variations_form');
    if (!$form.length) {
        console.log('No variations form found');
        return;
    }
    
    var $product = $form.closest('.product');
    var $productGallery = $product.find('.images');
    var originalGallery = $productGallery.html();
    
    console.log('WC Variant Gallery: Initializing');
    console.log('Product gallery found:', $productGallery.length);
    
    // Store original gallery
    $form.data('original-gallery', originalGallery);
    
    // Listen for variation changes
    $form.on('found_variation', function(event, variation) {
        console.log('Variation found:', variation);
        
        if (variation && variation.variant_gallery && variation.variant_gallery.length > 0) {
            console.log('Using variant gallery with', variation.variant_gallery.length, 'images');
            updateVariantGallery(variation.variant_gallery);
        } else {
            console.log('No variant gallery, restoring original');
            restoreOriginalGallery();
        }
    });
    
    // Reset when no variation is selected
    $form.on('reset_data', function() {
        console.log('Resetting to original gallery');
        restoreOriginalGallery();
    });
    
    function updateVariantGallery(galleryData) {
        console.log('Updating variant gallery with', galleryData.length, 'images');
        
        // Don't change the main image - let WooCommerce handle the main variant image
        // Just create the thumbnail gallery below
        createThumbnailGallery(galleryData);
        
        console.log('Variant gallery updated successfully');
    }
    
    function createThumbnailGallery(galleryData) {
        // Remove existing thumbnail gallery if it exists
        $productGallery.find('.variant-thumbnail-gallery').remove();
        
        if (galleryData.length < 1) {
            return; // No images to show
        }
        
        // Create thumbnail gallery HTML
        var thumbnailHTML = '<div class="variant-thumbnail-gallery" style="margin-top: 10px; display: flex; gap: 10px; flex-wrap: wrap;">';
        
        galleryData.forEach(function(image, index) {
            var activeClass = index === 0 ? ' active' : '';
            thumbnailHTML += '<div class="variant-thumbnail' + activeClass + '" data-image-index="' + index + '" style="width: 80px; height: 80px; border: 2px solid #ddd; cursor: pointer; overflow: hidden; border-radius: 4px;">';
            thumbnailHTML += '<img src="' + (image.thumb || image.src) + '" alt="' + (image.alt || '') + '" style="width: 100%; height: 100%; object-fit: cover;" />';
            thumbnailHTML += '</div>';
        });
        
        thumbnailHTML += '</div>';
        
        // Add thumbnail gallery after the main image
        $productGallery.append(thumbnailHTML);
        
        // Add click handlers for thumbnails - open in lightbox
        $productGallery.find('.variant-thumbnail').on('click', function() {
            var imageIndex = $(this).data('image-index');
            var image = galleryData[imageIndex];
            
            console.log('Thumbnail clicked, opening lightbox for image:', image.src);
            
            if (image) {
                // Create custom lightbox
                createCustomLightbox(image);
                
                // Update active thumbnail
                $productGallery.find('.variant-thumbnail').removeClass('active');
                $(this).addClass('active');
                
                console.log('Lightbox opened for:', image.src);
            }
        });
        
        // Add CSS for active state and lightbox
        if (!$('#variant-gallery-styles').length) {
            $('head').append('<style id="variant-gallery-styles">' +
                '.variant-thumbnail.active { border-color: #0073aa !important; } ' +
                '.variant-thumbnail:hover { border-color: #0073aa; } ' +
                '.variant-lightbox { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.9); z-index: 9999; display: flex; align-items: center; justify-content: center; overflow: scroll; } ' +
                '.variant-lightbox-content { position: relative; max-width: 90%; max-height: 90%; } ' +
                '.variant-lightbox img { max-width: 100%; max-height: 100%; object-fit: contain; } ' +
                '.variant-lightbox-close { position: absolute; top: -40px; right: 0; background: #fff; color: #000; border: none; width: 30px; height: 30px; border-radius: 50%; cursor: pointer; font-size: 18px; font-weight: bold; display: flex; align-items: center; justify-content: center; } ' +
                '.variant-lightbox-close:hover { background: #f0f0f0; } ' +
                '</style>');
        }
    }
    
    function createCustomLightbox(image) {
        // Remove any existing lightbox
        $('.variant-lightbox').remove();
        
        // Create lightbox HTML
        var lightboxHTML = '<div class="variant-lightbox">' +
            '<div class="variant-lightbox-content">' +
            '<button class="variant-lightbox-close">&times;</button>' +
            '<img src="' + (image.full || image.src) + '" alt="' + (image.alt || '') + '" />' +
            '</div>' +
            '</div>';
        
        // Add to body
        $('body').append(lightboxHTML);
        
        // Add close functionality
        $('.variant-lightbox-close, .variant-lightbox').on('click', function(e) {
            if (e.target === this) {
                $('.variant-lightbox').remove();
            }
        });
        
        // Close on Escape key
        $(document).on('keydown.variant-lightbox', function(e) {
            if (e.keyCode === 27) { // Escape key
                $('.variant-lightbox').remove();
                $(document).off('keydown.variant-lightbox');
            }
        });
    }
    
    function restoreOriginalGallery() {
        var original = $form.data('original-gallery');
        if (original) {
            $productGallery.html(original);
            console.log('Restored original gallery');
        }
    }
});
