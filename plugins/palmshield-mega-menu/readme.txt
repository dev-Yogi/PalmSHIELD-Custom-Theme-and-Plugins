=== PalmSHIELD Mega Menu ===
Contributors: palmshield
Tags: mega menu, navigation, menu, dropdown
Requires at least: 5.0
Tested up to: 6.4
Stable tag: 1.0.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Custom mega menu navigation for PalmSHIELD website with hover dropdowns and responsive design.

== Description ==

A custom mega menu plugin designed specifically for PalmSHIELD, featuring:

* 5 main navigation items: Screens, Enclosures, Site Amenities, Gates, Resources
* Organized mega menu dropdowns with categorized links
* Featured boxes with call-to-action buttons
* Fully responsive design with mobile hamburger menu
* Admin panel for easy link management
* Shortcode and action hook support for flexible integration

== Installation ==

1. Upload the `palmshield-mega-menu` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to 'Mega Menu' in the admin sidebar to configure your links
4. Add the menu to your theme using one of these methods:

**Shortcode:**
`[palmshield_mega_menu]`

**PHP (in theme files):**
`<?php do_action('palmshield_mega_menu'); ?>`

== Usage ==

= Adding to Your Theme =

The easiest way to add the mega menu to your theme is to replace your existing header navigation.

In your theme's `header.php` file, replace your current navigation with:

```php
<?php do_action('palmshield_mega_menu'); ?>
```

Or use the shortcode in any page/post:

```
[palmshield_mega_menu]
```

= Configuring Menu Links =

1. Go to **Mega Menu** in the WordPress admin sidebar
2. Click on the tab for the menu section you want to edit (Screens, Enclosures, etc.)
3. Update the titles, descriptions, and URLs for each menu item
4. Click **Save Changes**

= Logo Settings =

In the Mega Menu settings page, you can configure:

* **Logo URL**: The full URL to your logo image
* **Logo Link**: Where the logo should link to (usually your homepage)

== Frequently Asked Questions ==

= How do I change the menu colors? =

The plugin uses CSS variables. You can override them in your theme's CSS:

```css
:root {
    --psmm-navy: #1e335f;
    --psmm-red: #c41230;
    --psmm-light-gray: #f5f7fa;
    --psmm-medium-gray: #e2e6eb;
    --psmm-dark-gray: #6b7280;
    --psmm-white: #ffffff;
}
```

= Can I add more menu items? =

The current version has a fixed structure optimized for PalmSHIELD's product catalog. For custom modifications, you can edit the template file at `templates/mega-menu.php`.

= Is the menu accessible? =

Yes! The menu includes:
* Proper ARIA labels and roles
* Keyboard navigation support
* Focus indicators
* Screen reader support

== Changelog ==

= 1.0.0 =
* Initial release
* 5 mega menu sections with full admin configuration
* Responsive mobile menu
* Shortcode and action hook support

== Upgrade Notice ==

= 1.0.0 =
Initial release of the PalmSHIELD Mega Menu plugin.
