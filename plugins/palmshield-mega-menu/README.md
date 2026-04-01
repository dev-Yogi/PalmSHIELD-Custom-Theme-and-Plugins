# PalmSHIELD Mega Menu

A custom WordPress plugin that provides an interactive mega menu navigation for PalmSHIELD websites.

## Features

- ✅ Hover-activated mega menus on desktop
- ✅ Touch-friendly mobile navigation
- ✅ Fully responsive design
- ✅ Keyboard accessible (WCAG compliant)
- ✅ Five pre-configured mega menus: Screens, Enclosures, Site Amenities, Gates, Resources
- ✅ Customizable via PHP filters
- ✅ Admin settings page for logo management
- ✅ No jQuery dependency for frontend (vanilla JavaScript)

## Installation

1. Download the plugin ZIP file
2. Go to WordPress Admin → Plugins → Add New → Upload Plugin
3. Select the ZIP file and click "Install Now"
4. Activate the plugin

Or manually:

1. Unzip the plugin folder
2. Upload `palmshield-mega-menu` folder to `/wp-content/plugins/`
3. Activate from Plugins page

## Usage

### Method 1: Shortcode

Add this shortcode to any page, post, or widget:

```
[palmshield_mega_menu]
```

### Method 2: PHP in Theme

Add this to your theme's `header.php` or template:

```php
<?php echo do_shortcode('[palmshield_mega_menu]'); ?>
```

### Method 3: Gutenberg Block

Use a "Shortcode" block and enter `[palmshield_mega_menu]`

## Configuration

### Logo Settings

Go to **WordPress Admin → Mega Menu** to:
- Upload/select a logo image
- Set the logo link URL

### Customizing Menu Links

Edit the file `includes/menu-data.php` to modify:
- Menu labels
- Link URLs
- Descriptions
- Featured box content

### Using PHP Filters

For programmatic customization, use the filter:

```php
add_filter('psmm_menu_data', function($menu_data) {
    // Modify screens section
    $menu_data['screens']['featured']['cta_url'] = '/custom-url/';
    
    // Add custom link
    $menu_data['screens']['columns'][0]['items'][] = array(
        'label' => 'New Item',
        'url' => '/new-item/',
        'description' => 'Description here'
    );
    
    return $menu_data;
});
```

## Styling Customization

### CSS Variables

Override these in your theme's CSS:

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

### Custom Styling

All plugin classes are prefixed with `psmm-` for easy targeting:

```css
/* Example: Change nav item padding */
.psmm-nav-item {
    padding: 20px 30px;
}

/* Example: Change featured box color */
.psmm-mega-featured {
    background: linear-gradient(135deg, #your-color 0%, #your-color-2 100%);
}
```

## Menu Structure

```
├── Screens
│   ├── By Application (Rooftop, Parking, Ground, Playgrounds)
│   ├── By Performance (Crash Rated, Acoustic, Vision)
│   ├── By Style (Louver, Perforated, Corrugated, Custom)
│   └── Featured: Help Me Choose
│
├── Enclosures
│   ├── Premiere Enclosures (Card)
│   ├── Covered Enclosures (Card)
│   └── Featured: Compare All Options
│
├── Site Amenities
│   ├── Railings
│   ├── Bollards
│   ├── Shades
│   └── Featured: Complete Your Project
│
├── Gates
│   ├── Access Control+
│   ├── Architectural Gates
│   ├── Vulcan Pedestrian Gates
│   ├── Hardware
│   └── Featured: Architect Resources
│
└── Resources
    ├── Technical Resources
    ├── Sales Resources
    ├── Support
    └── Featured: Request a Quote
```

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile Safari (iOS 12+)
- Chrome Mobile (Android 8+)

## Troubleshooting

### Menu not appearing
- Ensure the shortcode is placed correctly
- Check for JavaScript errors in browser console
- Verify plugin is activated

### Styling conflicts
- Use browser inspector to identify conflicting styles
- Add more specific selectors or use `!important` if needed
- Check if your theme has mega menu styles that need to be overridden

### Mobile menu not working
- Ensure JavaScript is loading
- Check for console errors
- Verify no other scripts are preventing click events

## Changelog

### 1.0.0
- Initial release
- Five mega menus (Screens, Enclosures, Site Amenities, Gates, Resources)
- Admin settings page
- Mobile responsive design
- Keyboard navigation support

## Support

For support, contact the PalmSHIELD web development team.

## License

GPL v2 or later
