jQuery(document).ready(function($) {
    // Initialize color picker
    $('.color-picker').wpColorPicker();

    // Media uploader
    $('.upload-image').click(function(e) {
        e.preventDefault();
        var button = $(this);
        var imageUploader = wp.media({
            title: 'Select Image',
            button: {
                text: 'Use this image'
            },
            multiple: false
        });

        imageUploader.on('select', function() {
            var attachment = imageUploader.state().get('selection').first().toJSON();
            button.siblings('.image-url').val(attachment.url);
        });

        imageUploader.open();
    });
}); 