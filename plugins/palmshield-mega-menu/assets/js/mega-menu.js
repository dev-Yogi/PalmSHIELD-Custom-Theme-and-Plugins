/**
 * PalmSHIELD Mega Menu JavaScript
 * Version: 1.0.0
 */

(function() {
    'use strict';

    // Wait for DOM to be ready
    document.addEventListener('DOMContentLoaded', function() {
        initMegaMenu();
    });

    function initMegaMenu() {
        const navContainer = document.querySelector('.psmm-nav-container');
        if (!navContainer) return;

        const navItems = navContainer.querySelectorAll('.psmm-nav-item[data-menu]');
        const megaMenus = navContainer.querySelectorAll('.psmm-mega-menu');
        const mobileToggle = navContainer.querySelector('.psmm-mobile-toggle');
        const navItemsContainer = navContainer.querySelector('.psmm-nav-items');
        
        let timeout;
        let isMobile = window.innerWidth <= 1024;

        // Check if mobile on resize
        window.addEventListener('resize', function() {
            isMobile = window.innerWidth <= 1024;
            if (!isMobile) {
                // Reset mobile states when switching to desktop
                if (mobileToggle) mobileToggle.classList.remove('active');
                if (navItemsContainer) navItemsContainer.classList.remove('active');
                megaMenus.forEach(menu => menu.classList.remove('active'));
                navItems.forEach(item => item.classList.remove('active'));
            }
        });

        // Desktop hover functionality
        function showMenu(menuId) {
            if (isMobile) return;
            
            clearTimeout(timeout);
            megaMenus.forEach(menu => menu.classList.remove('active'));
            navItems.forEach(item => item.classList.remove('active'));
            
            const menu = navContainer.querySelector('#psmm-menu-' + menuId);
            const navItem = navContainer.querySelector('[data-menu="' + menuId + '"]');
            
            if (menu) {
                menu.classList.add('active');
                if (navItem) navItem.classList.add('active');
            }
        }

        function hideMenus() {
            if (isMobile) return;
            
            timeout = setTimeout(function() {
                megaMenus.forEach(menu => menu.classList.remove('active'));
                navItems.forEach(item => item.classList.remove('active'));
            }, 150);
        }

        // Desktop event listeners
        navItems.forEach(function(item) {
            item.addEventListener('mouseenter', function() {
                if (!isMobile) {
                    showMenu(this.dataset.menu);
                }
            });

            // Click handler for both mobile and desktop
            item.addEventListener('click', function(e) {
                if (isMobile) {
                    e.preventDefault();
                    const menuId = this.dataset.menu;
                    const menu = navContainer.querySelector('#psmm-menu-' + menuId);
                    
                    // Toggle current menu
                    if (menu) {
                        const isActive = menu.classList.contains('active');
                        
                        // Close all menus first
                        megaMenus.forEach(m => m.classList.remove('active'));
                        navItems.forEach(i => i.classList.remove('active'));
                        
                        // Open clicked menu if it wasn't already open
                        if (!isActive) {
                            menu.classList.add('active');
                            this.classList.add('active');
                        }
                    }
                }
            });

            // Keyboard accessibility
            item.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    if (isMobile) {
                        this.click();
                    } else {
                        showMenu(this.dataset.menu);
                    }
                }
                if (e.key === 'Escape') {
                    hideMenus();
                }
            });
        });

        // Keep menu open when hovering over mega menu content
        megaMenus.forEach(function(menu) {
            menu.addEventListener('mouseenter', function() {
                if (!isMobile) {
                    clearTimeout(timeout);
                }
            });
            
            menu.addEventListener('mouseleave', function() {
                if (!isMobile) {
                    hideMenus();
                }
            });
        });

        // Close menu when leaving nav container
        navContainer.addEventListener('mouseleave', function() {
            if (!isMobile) {
                hideMenus();
            }
        });

        // Mobile toggle
        if (mobileToggle) {
            mobileToggle.addEventListener('click', function() {
                this.classList.toggle('active');
                if (navItemsContainer) {
                    navItemsContainer.classList.toggle('active');
                }
                // Close all mega menus when toggling mobile menu
                megaMenus.forEach(menu => menu.classList.remove('active'));
                navItems.forEach(item => item.classList.remove('active'));
            });
        }

        // Close menus when clicking outside
        document.addEventListener('click', function(e) {
            if (!navContainer.contains(e.target)) {
                megaMenus.forEach(menu => menu.classList.remove('active'));
                navItems.forEach(item => item.classList.remove('active'));
                if (mobileToggle) mobileToggle.classList.remove('active');
                if (navItemsContainer) navItemsContainer.classList.remove('active');
            }
        });

        // Handle escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                megaMenus.forEach(menu => menu.classList.remove('active'));
                navItems.forEach(item => item.classList.remove('active'));
                if (mobileToggle) mobileToggle.classList.remove('active');
                if (navItemsContainer) navItemsContainer.classList.remove('active');
            }
        });

        // Search expansion functionality
        const searchContainer = navContainer.querySelector('.psmm-search-container');
        if (searchContainer) {
            const searchInput = searchContainer.querySelector('.psmm-search-input');
            const searchForm = searchContainer.querySelector('.psmm-search-form');
            let searchTimeout;
            
            // Focus input when container is hovered (desktop only)
            searchContainer.addEventListener('mouseenter', function() {
                if (!isMobile && searchInput) {
                    clearTimeout(searchTimeout);
                    // Small delay to allow expansion animation
                    searchTimeout = setTimeout(function() {
                        if (searchContainer.matches(':hover')) {
                            searchInput.focus();
                        }
                    }, 150);
                }
            });

            // Keep expanded when input is focused
            if (searchInput) {
                searchInput.addEventListener('focus', function() {
                    if (!isMobile) {
                        searchContainer.classList.add('expanded');
                    }
                });

                // Handle blur - collapse if not hovering
                searchInput.addEventListener('blur', function() {
                    if (!isMobile) {
                        // Check if still hovering over container
                        setTimeout(function() {
                            if (!searchContainer.matches(':hover') && !searchInput.matches(':focus')) {
                                searchContainer.classList.remove('expanded');
                                // Clear input if empty
                                if (!searchInput.value.trim()) {
                                    searchInput.value = '';
                                }
                            }
                        }, 100);
                    }
                });
            }

            // Handle form submission
            if (searchForm && searchInput) {
                searchForm.addEventListener('submit', function(e) {
                    const query = searchInput.value.trim();
                    if (!query) {
                        e.preventDefault();
                        searchInput.focus();
                        return false;
                    }
                });
            }

            // Prevent collapse when clicking inside search container
            searchContainer.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }
    }
})();
