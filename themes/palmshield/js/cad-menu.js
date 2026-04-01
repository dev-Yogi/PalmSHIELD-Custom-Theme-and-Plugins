$(document).ready(function() {
    // Find all h2 elements
    const $headings = $('h2');
    
    // Create navigation container and list
    const $navContainer = $('<div>', {
        class: 'cad-nav-container'
    });
    
    const $navList = $('<ul>', {
        class: 'cad-nav-list'
    });
    
    // Loop through headings and create navigation items
    $headings.each(function() {
        const id = $(this).attr('id');
        const text = $(this).text();
        
        // Create list item with anchor
        const $listItem = $('<li>', {
            class: 'cad-nav-item'
        });
        
        const $anchor = $('<a>', {
            href: '#' + id,
            text: text
        });
        
        // Add smooth scroll behavior
        $anchor.on('click', function(e) {
            e.preventDefault();
            $('html, body').animate({
                scrollTop: $('#' + id).offset().top
            }, 'smooth');
            
            // Update URL with hash
            history.pushState(null, null, '#' + id);
        });
        
        // Append anchor to list item and list item to nav list
        $listItem.append($anchor);
        $navList.append($listItem);
    });
    
    // Append navigation list to container
    $navContainer.append($navList);
    
    // Insert navigation after the opening of product-cad-files-wrapper
    const $productWrapper = $('.product-cad-files-wrapper');
    if ($productWrapper.length) {
        $productWrapper.prepend($navContainer);
    }
});
