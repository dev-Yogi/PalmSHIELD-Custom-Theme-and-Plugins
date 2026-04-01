/**
 * card-block.js
 * Frontend enhancements for Product Category Card blocks.
 *
 * Three responsibilities:
 *  1. Fade-in cards cleanly once their background image is loaded
 *     (avoids a flash of the skeleton colour for lazy images).
 *  2. Prefetch card-button destinations on first hover — so the linked
 *     page starts loading before the user clicks.
 *  3. Graceful fallback when an image fails to load.
 *
 * No dependencies. Loads in the footer (non-render-blocking).
 */

( function () {
    'use strict';

    /* ------------------------------------------------------------------
       1. Image fade-in
       ------------------------------------------------------------------ */

    /**
     * Mark an image as loaded so the CSS opacity transition fires.
     * For eager/high-priority images the browser may have already decoded
     * them before this script runs — we handle both cases.
     */
    function markImageLoaded( img ) {
        img.classList.add( 'is-loaded' );
    }

    function initImageFadeIn( cards ) {
        cards.forEach( function ( card ) {
            var img = card.querySelector( '.card-bg-image' );
            if ( ! img ) return;

            if ( img.complete && img.naturalWidth > 0 ) {
                // Already in cache or loaded synchronously.
                markImageLoaded( img );
            } else {
                img.addEventListener( 'load', function () {
                    markImageLoaded( img );
                }, { once: true } );
            }
        } );
    }

    /* ------------------------------------------------------------------
       2. Prefetch link destinations on first hover
       Inserts a <link rel="prefetch"> so the next page is in cache by
       the time the user's click resolves.
       ------------------------------------------------------------------ */

    var prefetched = new Set();

    function prefetch( url ) {
        if ( ! url || prefetched.has( url ) ) return;

        // Skip cross-origin URLs — prefetch is most useful for same-origin pages.
        try {
            var parsed = new URL( url, window.location.href );
            if ( parsed.origin !== window.location.origin ) return;
        } catch ( e ) {
            return;
        }

        prefetched.add( url );

        var link = document.createElement( 'link' );
        link.rel  = 'prefetch';
        link.href = url;
        document.head.appendChild( link );
    }

    function initPrefetchOnHover( cards ) {
        cards.forEach( function ( card ) {
            var btn = card.querySelector( '.card-button' );
            if ( ! btn ) return;

            btn.addEventListener( 'mouseenter', function () {
                prefetch( btn.getAttribute( 'href' ) );
            }, { once: true } );

            // Also prefetch on touch start for mobile users.
            btn.addEventListener( 'touchstart', function () {
                prefetch( btn.getAttribute( 'href' ) );
            }, { once: true, passive: true } );
        } );
    }

    /* ------------------------------------------------------------------
       3. Image error handling
       If an image 404s or otherwise fails, hide the broken-image icon
       and let the card skeleton colour show through cleanly.
       ------------------------------------------------------------------ */

    function initImageErrorHandling( cards ) {
        cards.forEach( function ( card ) {
            var img = card.querySelector( '.card-bg-image' );
            if ( ! img ) return;

            img.addEventListener( 'error', function () {
                img.style.display = 'none';
                card.classList.add( 'card--no-image' );
            }, { once: true } );
        } );
    }

    /* ------------------------------------------------------------------
       Boot
       ------------------------------------------------------------------ */

    function init() {
        var cards = Array.prototype.slice.call(
            document.querySelectorAll( '.product-card-block' )
        );

        if ( cards.length === 0 ) return;

        initImageFadeIn( cards );
        initPrefetchOnHover( cards );
        initImageErrorHandling( cards );
    }

    // DOMContentLoaded fires before images finish loading, which is exactly
    // when we want to attach our load/error listeners.
    if ( document.readyState === 'loading' ) {
        document.addEventListener( 'DOMContentLoaded', init );
    } else {
        init();
    }

} )();