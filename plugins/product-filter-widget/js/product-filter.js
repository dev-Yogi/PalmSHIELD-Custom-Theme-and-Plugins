jQuery(document).ready(function($) {
    $('.product-filter-widget form').on('change', 'input', function(e) {
        e.preventDefault();

        var $form = $(this).closest('form');
        var data = $form.serialize();

        $.ajax({
            url: productFilterAjax.ajax_url,
            type: 'GET',
            data: data + '&action=filter_products',
            beforeSend: function() {
                // Optionally, show a loading indicator
            },
            success: function(response) {
                if (response.success) {
                    var productListHtml = '';

                    // Loop through the products returned
                    $.each(response.data, function(index, product) {
                        if (product.message) {
                            productListHtml += '<p>' + product.message + '</p>';
                        } else {
                            productListHtml += '<div class="product-item">';
                            productListHtml += '<a href="' + product.link + '">';
                            productListHtml += '<img src="' + product.thumbnail + '" alt="' + product.title + '">';
                            productListHtml += '<h2>' + product.title + '</h2>';
                            productListHtml += '</a>';
                            productListHtml += '</div>';
                        }
                    });

                    $('.product-list').html(productListHtml);
                } else {
                    $('.product-list').html('<p>An error occurred while fetching products.</p>');
                }
            },
            error: function() {
                $('.product-list').html('<p>An unexpected error occurred. Please try again later.</p>');
            }
        });
    });
});
