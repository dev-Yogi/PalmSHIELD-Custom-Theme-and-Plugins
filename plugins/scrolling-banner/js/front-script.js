jQuery(document).ready(function($) {
    console.log('Scrolling banner script loaded');
    
    function initBanner() {
        var $banner = $('.scrolling-banner-content');
        var $container = $('.scrolling-banner');
        
        if ($banner.length === 0) {
            console.log('No banner elements found on page');
            return;
        }

        // Handle close button
        $('.scrolling-banner-close').on('click', function() {
            $container.slideUp(300, function() {
                $(this).remove();
            });
            var duration = parseInt($container.data('cookie-duration')) || 0;
            setCookie('scrolling_banner_closed', 'true', duration);
        });

        // Calculate and set animation duration based on content width
        function updateScrollSpeed() {
            var contentWidth = $banner[0].scrollWidth;
            var containerWidth = $container.width();
            var speed = parseInt($banner.data('speed')) || 60; // Default speed increased to 60
            
            // New calculation for duration - slower for longer content
            var duration = (contentWidth / containerWidth) * speed;
            
            // Ensure minimum duration of 20 seconds
            duration = Math.max(duration, 20);

            $banner.css({
                'animation-duration': duration + 's',
                '-webkit-animation-duration': duration + 's'
            });

            // Clone content if it's shorter than the container
            if (contentWidth < containerWidth * 2) {
                $banner.html($banner.html() + ' &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ' + $banner.html());
            }
        }

        // Initial setup
        updateScrollSpeed();

        // Update on window resize
        $(window).on('resize', updateScrollSpeed);

        // Pause animation on hover
        $banner.hover(
            function() { $(this).css('animation-play-state', 'paused'); },
            function() { $(this).css('animation-play-state', 'running'); }
        );
    }

    // Initialize banner
    initBanner();

    // Modified setCookie function to handle 0 duration (session cookie)
    function setCookie(name, value, days) {
        var expires = '';
        if (days > 0) { // Only set expiry if days is greater than 0
            var date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = '; expires=' + date.toUTCString();
        }
        document.cookie = name + '=' + (value || '') + expires + '; path=/';
        console.log('Cookie set:', name, 'with expiry:', expires || 'session'); // Debug log
    }

    function getCookie(name) {
        var nameEQ = name + '=';
        var ca = document.cookie.split(';');
        for(var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) === ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    }
}); 