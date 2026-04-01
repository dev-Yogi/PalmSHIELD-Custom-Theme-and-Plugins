jQuery(document).ready(function($) {
    'use strict';
    
    console.log('WC Variant Gallery Admin: Script loaded');
    
    // Add gallery images functionality
    $(document).on('click', '.add-gallery-images', function(e) {
        e.preventDefault();
        console.log('Add gallery images clicked');
        
        var $container = $(this).closest('.variant-gallery-container');
        var $imagesContainer = $container.find('.variant-gallery-images');
        var variationId = $container.data('variation-id');
        
        console.log('Container found:', $container.length, 'Variation ID:', variationId);
        
        // Check if wp.media is available
        if (typeof wp === 'undefined' || !wp.media) {
            console.error('wp.media is not available');
            alert('Media library is not available. Please refresh the page and try again.');
            return;
        }
        
        var mediaUploader = wp.media({
            title: 'Select Gallery Images',
            button: {
                text: 'Add to Gallery'
            },
            multiple: true
        });
        
        mediaUploader.on('select', function() {
            var attachments = mediaUploader.state().get('selection').toJSON();
            console.log('Selected attachments:', attachments);
            
            attachments.forEach(function(attachment) {
                var thumbnailUrl = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;
                
                var imageHtml = '<div class="variant-gallery-image" data-image-id="' + attachment.id + '">';
                imageHtml += '<img src="' + thumbnailUrl + '" alt="' + (attachment.alt || '') + '" />';
                imageHtml += '<button type="button" class="remove-image">×</button>';
                imageHtml += '</div>';
                
                $imagesContainer.append(imageHtml);
            });
        });
        
        mediaUploader.open();
    });
    
    // Remove image functionality
    $(document).on('click', '.remove-image', function(e) {
        e.preventDefault();
        console.log('Remove image clicked');
        $(this).closest('.variant-gallery-image').remove();
    });
    
    // Save gallery functionality
    $(document).on('click', '.save-gallery', function(e) {
        e.preventDefault();
        console.log('Save gallery clicked');
        
        var $container = $(this).closest('.variant-gallery-container');
        var $status = $container.find('.gallery-status');
        var variationId = $container.data('variation-id');
        var imageIds = [];
        
        $container.find('.variant-gallery-image').each(function() {
            imageIds.push($(this).data('image-id'));
        });
        
        console.log('Saving gallery for variation', variationId, 'with images:', imageIds);
        
        $status.html('Saving...');
        
        $.ajax({
            url: variant_gallery_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'save_variant_gallery',
                variation_id: variationId,
                gallery_images: imageIds,
                nonce: variant_gallery_ajax.nonce
            },
            success: function(response) {
                console.log('Save response:', response);
                if (response.success) {
                    $status.html('<span style="color: green;">✓ Gallery saved successfully (' + response.data.image_count + ' images)</span>');
                } else {
                    $status.html('<span style="color: red;">✗ Error saving gallery</span>');
                }
            },
            error: function(xhr, status, error) {
                console.error('Save error:', status, error);
                $status.html('<span style="color: red;">✗ Error saving gallery</span>');
            }
        });
    });
});
