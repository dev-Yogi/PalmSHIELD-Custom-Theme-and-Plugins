jQuery(function($){
    $('.canopy-products-grid-inner').on('click', '.canopy-add-to-quote-btn', function(e){
        e.preventDefault();
        var $btn = $(this);
        var product_id = $btn.data('product_id');
        var variation_id = $btn.data('variation_id');
        var attributes = $btn.data('attributes');
        $btn.prop('disabled', true);
        $btn.siblings('.canopy-quote-status').text('Adding...');
        $.post(CanopyGridAjax.ajax_url, {
            action: 'canopy_add_to_quote',
            nonce: CanopyGridAjax.nonce,
            product_id: product_id,
            variation_id: variation_id,
            attributes: attributes
        }, function(response){
            if(response.success){
                $btn.siblings('.canopy-quote-status').text(response.data.message);
                $btn.hide();
            } else {
                $btn.siblings('.canopy-quote-status').text(response.data.message);
                $btn.prop('disabled', false);
            }
        });
    });
});
