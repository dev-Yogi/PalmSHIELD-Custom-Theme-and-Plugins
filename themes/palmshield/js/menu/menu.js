jQuery(document).ready(function($) {
    $('ul.header-menu .menu-item-has-children > a').on('click', function(e) {
        var $this = $(this);
        var $subMenu = $this.siblings('.sub-menu');

        // Check if the submenu is visible or if the click was on the caret
        if ($subMenu.is(':visible') || $(e.target).closest('.submenu-caret').length > 0) {
            e.preventDefault();
            $subMenu.slideToggle(); // Toggle submenu
            $this.children('.submenu-caret').toggleClass('open'); // Optional: rotate caret
        }
    });

    // Check if the current URL contains '/palmshades/'
    if (window.location.pathname.includes('/palmshades/') || window.location.pathname.includes('/retractable-canopies/')) {
        // Replace the logo image source
        $('.navbar-brand a img').attr('src', '/wp-content/uploads/2024/10/logo-palmshade-and-shield.png');
        $('.footer-area .wp-block-image img').attr('src', '/wp-content/uploads/2024/10/logo-palmshade-and-shield.png');
        $('.footer-area .wp-block-image img').attr('srcset', '/wp-content/uploads/2024/10/logo-palmshade-and-shield.png');
    }

   
    // Add palmshades section header color change
    if (window.location.pathname.includes('/palmshades/') || window.location.pathname.includes('/retractable-canopies/') ||
        (window.location.search.includes('site=shades'))) {
        console.log('Palmshades section detected');
        $('header.site-header, .search-btn').css('background-color', '#ef8d27');
        $('#mega-menu-wrap-screens-main-menu #mega-menu-screens-main-menu > li.mega-menu-item > a.mega-menu-link ').css('color', '#ffffff');
        $('ul.utility-menu li a').css('color', '#fff');
        $('.fa-search, .search-input').css('color', '#e02826');
        $('.search-input:focus').css('border', 'solid 1px #e02826');
        $('.cta-button').css('background-color', '#fff');
        $('.cta-button').css('color', '#e02826');
        $('.cta-button').css('border', 'solid 1px #e02826');
    }


    // Add scroll detection variables
    let lastScrollTop = 0;
    const header = $('.parent-header');
    const headerHeight = header.outerHeight();

    // Handle scroll events
    $(window).scroll(function() {
        const currentScroll = $(this).scrollTop();
        
        if (currentScroll > lastScrollTop && currentScroll > headerHeight) {
            // Scrolling down - hide header
            header.addClass('header-hidden');
        } else {
            // Scrolling up - show header
            header.removeClass('header-hidden');
        }
        
        lastScrollTop = currentScroll;
    });
});
