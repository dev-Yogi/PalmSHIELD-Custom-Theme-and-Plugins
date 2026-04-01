# PalmShield Color Tool - Custom Plugin Installation Guide

## 📦 Simple Installation


**Image naming is CRITICAL:**
- ✅ `PANELBrilliantBlue.png`
- ✅ `POSTEmeraldGreen.png`
- ❌ `panel-brilliant-blue.png`
- ❌ `Panel_Brilliant_Blue.png`

### Step 5: Add to a Page

1. Create or edit any page
2. Add a **Shortcode block**
3. Type: `[palmshield_color_tool]`
4. Preview or publish!

---

## ⚙️ Configuration

### Update Image Path (if needed)

If your images are not in `wp-content/uploads/palmshield-colors/`, edit the plugin file:

1. Open `/wp-content/plugins/palmshield-color-tool/palmshield-color-tool-plugin.php`
2. Find line 23:
```php
$image_base_url = wp_get_upload_dir()['baseurl'] . '/palmshield-colors/';
```
3. Change `/palmshield-colors/` to match your folder name

### Add More Colors

Edit the plugin file and find the `colorOptions` array (around line 167):

```javascript
const colorOptions = [
    { id: 'BrilliantBlue', name: 'Brilliant Blue', hex: '#4A90E2' },
    { id: 'EmeraldGreen', name: 'Emerald Green', hex: '#50C878' },
    // Add your new color here:
    { id: 'SignalRed', name: 'Signal Red', hex: '#D90000' },
];
```

**Remember:** The `id` must match your image filename!
- If `id: 'SignalRed'` then you need:
  - `PANELSignalRed.png`
  - `POSTSignalRed.png`

---

## 📝 Using the Shortcode

### Basic Usage
```
[palmshield_color_tool]
```

That's it! Just add this shortcode to any page or post.

### Where to Add It

**In Block Editor (Gutenberg):**
1. Click the **+** button
2. Search for "Shortcode"
3. Add the Shortcode block
4. Type: `[palmshield_color_tool]`

**In Classic Editor:**
1. Switch to Text mode
2. Paste: `[palmshield_color_tool]`

**In Page Builders:**
- **Elementor:** Use the Shortcode widget
- **Divi:** Use the Shortcode module
- **Beaver Builder:** Use the Shortcode module

---

## 🎨 Admin Panel

After activation, you'll see a new menu item in WordPress Admin:

**PalmShield Colors**

This admin page includes:
- Complete usage instructions
- Image naming rules
- How to add new colors
- Troubleshooting tips

---

## ✅ Quick Checklist

Before going live:

- [ ] Plugin folder created: `/wp-content/plugins/palmshield-color-tool/`
- [ ] Plugin file uploaded to that folder
- [ ] Plugin activated in WordPress
- [ ] Images uploaded to Media Library
- [ ] Images named correctly (PANEL/POSTColorName.png)
- [ ] Image path configured in plugin file
- [ ] Colors added to colorOptions array
- [ ] Shortcode added to test page
- [ ] Tool displays correctly
- [ ] Dropdowns populate with colors
- [ ] Images load when colors selected
- [ ] Tested on mobile device

---

## 🔍 Troubleshooting

### Plugin doesn't appear in Plugins list?

**Check:**
1. File is in correct location: `/wp-content/plugins/palmshield-color-tool/palmshield-color-tool-plugin.php`
2. File has proper PHP opening tag: `<?php`
3. Plugin header is intact (first 10 lines)

### Images not loading?

**Check browser console (F12):**
- Look for 404 errors showing the path WordPress is trying
- Verify that path matches where your images actually are
- Check image file names match exactly (case-sensitive!)

**Common fixes:**
1. Update `$image_base_url` in plugin file (line 23)
2. Verify image naming: `PANELColorName.png` not `panel-color-name.png`
3. Ensure images are uploaded to WordPress (not just server)
4. Check file permissions (files should be 644, folders 755)

### Colors not appearing in dropdown?

**Check:**
1. Colors are added to `colorOptions` array in plugin file
2. Array syntax is correct (commas between items)
3. No JavaScript errors in console

### Tool not displaying at all?

**Check:**
1. Plugin is activated
2. Shortcode is spelled correctly: `[palmshield_color_tool]`
3. No theme conflicts (try default WordPress theme temporarily)

---

## 🔧 Advanced Options

### Change Tool Title

Edit plugin file, find:
```html
<h1 class="tool-title">PalmShield Color Customization Tool</h1>
```

### Change Colors/Styling

All CSS is in the plugin file starting around line 30. You can modify:
- Colors: Search for `#002B57` (brand blue)
- Fonts: Change `font-family` values
- Spacing: Adjust `padding` and `margin` values
- Layout: Modify the grid at `.tool-container`

### Add More Features

The plugin structure allows easy expansion:
- Add more dropdown sections
- Include pricing calculator
- Add "Add to Cart" functionality
- Save/share configurations

---

## 📄 File You Need

Download this file from your outputs folder:
- **palmshield-color-tool-plugin.php**

That's the only file you need to create the plugin!

---

## 💡 Pro Tips

1. **Test first:** Use only 2-3 colors initially to verify everything works
2. **Stage it:** Test on staging site before production
3. **Backup:** Keep a backup of the plugin file with your customizations
4. **Document:** Note any custom colors you add for future reference
5. **Version control:** Increment plugin version number when you make changes

---

**That's it! You now have a custom WordPress plugin that adds the color tool to any page with a simple shortcode.**