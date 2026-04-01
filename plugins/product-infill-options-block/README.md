# Product Infill Options Block v3.0

A WordPress ACF block featuring a **two-tier tab system** for displaying product categories with nested orientation tabs, infill options, and color galleries. Designed for PalmSHIELD products and similar B2B product catalogs.

## What's New in Version 3.0

### Major Change: Two-Tier Tab System

**Before (v2.0):** Separate blocks for each product category required page scrolling
**After (v3.0):** All categories in one unified tab interface

```
┌─────────────────────────────────────────────────────────────────────────────┐
│ LOUVERED │ SEMI-PRIVATE │ SOLID │ STAGGERED │ EXPANDED METAL │ ... │ TRELLIS │  ← Primary Tabs
├─────────────────────────────────────────────────────────────────────────────┤
│                     LOUVERED INFILL OPTIONS                                  │
│  PalmSHIELD's most iconic infill, louvered panels provide maximum airflow... │
├─────────────────────────────────────────────────────────────────────────────┤
│      HORIZONTAL     │      VERTICAL      │     DIAGONAL     │    COLORS     │  ← Secondary Tabs
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                              │
│   [Product 1]    [Product 2]    [Product 3]    [Product 4]                  │  ← Product Grid
│   Aluminum Blade  Aluminum Plank  Composite      Vinyl Plank                │
│                                                                              │
└─────────────────────────────────────────────────────────────────────────────┘
```

---

## Features

✅ **Two-Tier Tab Navigation** - Primary category tabs + secondary orientation tabs  
✅ **Unlimited Categories** - Add as many product categories as needed  
✅ **Flexible Orientation Tabs** - Horizontal, Vertical, Diagonal, or custom labels  
✅ **Automatic Colors Tab** - Add color galleries per category  
✅ **File Downloads** - Attach CAD files, PDFs, specs with badge icons  
✅ **Customizable Colors** - Set active tab color and header background  
✅ **Responsive Design** - Horizontal scroll on mobile for categories  
✅ **Show/Hide Controls** - Toggle categories and tabs without deleting content  

---

## Installation

1. **Upload Files**
   ```
   /wp-content/plugins/product-infill-options-block/
   ├── index.php
   ├── block.json
   ├── block.css
   ├── render.php
   ├── acf-fields-registration.php
   └── README.md
   ```

2. **Activate Plugin**
   - WordPress Admin → Plugins → Activate "Product Infill Options Block"

3. **Requirements**
   - WordPress 6.1+
   - ACF Pro (for repeater fields)
   - PHP 7.4+

---

## Data Structure

```
Product Infill Options Block
│
├── Global Settings
│   ├── Grid Columns (2-5)
│   ├── Grid Gap (small/medium/large)
│   ├── Primary Tab Color
│   └── Header Background Color
│
└── Product Categories (Primary Tabs)
    ├── Category 1: "Louvered"
    │   ├── Category Description
    │   ├── Orientation Tabs (Secondary Tabs)
    │   │   ├── "Horizontal"
    │   │   │   └── Infill Options [image, caption, link, files]
    │   │   ├── "Vertical"
    │   │   │   └── Infill Options [...]
    │   │   └── "Diagonal"
    │   │       └── Infill Options [...]
    │   └── Color Galleries (Auto "Colors" tab)
    │       ├── "Aluminum Colors" [swatches]
    │       └── "Vinyl Colors" [swatches]
    │
    ├── Category 2: "Semi-Private"
    │   ├── ...
    │
    └── Category N: "Trellis"
        └── ...
```

---

## Usage Example: PalmSHIELD Screening Products

### Step 1: Add the Block
Insert "Product Infill Options Block" in the Gutenberg editor

### Step 2: Configure Global Settings
- Grid Columns: **4**
- Primary Color: **#C41E3A** (PalmSHIELD red)
- Header Background: **#2c2c54** (Navy blue)

### Step 3: Add Categories

**Category 1: Louvered**
```
├── Description: "PalmSHIELD's most iconic infill..."
├── Orientation Tabs:
│   ├── Horizontal → [Aluminum Blade, Aluminum Plank, Composite Plank, Vinyl Plank]
│   ├── Vertical → [Aluminum Blade, Aluminum Plank, ...]
│   └── Diagonal → [Aluminum Blade, ...]
└── Color Galleries:
    ├── Aluminum Colors (12 swatches)
    ├── Composite Colors (6 swatches)
    └── Vinyl Colors (8 swatches)
```

**Category 2: Semi-Private**
```
├── Description: "Partial visual screening with..."
├── Orientation Tabs:
│   ├── Horizontal → [...]
│   └── Vertical → [...]
└── Color Galleries: [...]
```

*(Repeat for: Solid, Staggered, Expanded Metal, Textured, Contemporary, Laser Cut, Illuminated, Faux Brick Stone, Open, Trellis)*

---

## Block Settings Reference

### Global Settings Tab

| Setting | Options | Default | Description |
|---------|---------|---------|-------------|
| Grid Columns | 2-5 | 4 | Product columns per row |
| Grid Gap | Small/Medium/Large | Medium | Spacing between products |
| Primary Tab Color | Color picker | #C41E3A | Active tab background |
| Header Background | Color picker | #2c2c54 | Section header background |

### Per-Category Settings

| Setting | Type | Description |
|---------|------|-------------|
| Category Name | Text | Label for primary tab (e.g., "Louvered") |
| Show Category | Toggle | Hide without deleting content |
| Category Icon | Image | Optional icon in tab |
| Category Description | WYSIWYG | Shows in header when category active |

### Per-Orientation Tab Settings

| Setting | Type | Description |
|---------|------|-------------|
| Tab Label | Text | Label for secondary tab (e.g., "Horizontal") |
| Show Tab | Toggle | Hide without deleting content |
| Tab Description | Text | Footer text below product grid |

### Per-Product Settings

| Setting | Type | Description |
|---------|------|-------------|
| Image | Image | Product photo |
| Caption | Text | Product name |
| Link URL | URL | Optional click-through link |
| File Resources | Repeater | Attach CAD, PDF, specs |

---

## Responsive Behavior

| Breakpoint | Category Tabs | Orientation Tabs | Product Grid |
|------------|---------------|------------------|--------------|
| Desktop (>1024px) | Horizontal scroll | Row of buttons | Selected columns |
| Tablet (810-1024px) | Horizontal scroll | Row of buttons | 3 columns |
| Mobile (600-810px) | Horizontal scroll | Row of buttons | 2 columns |
| Small (<600px) | Horizontal scroll | Stacked vertically | 2 columns |

---

## Migration from Version 2.0

### What Changes

| v2.0 | v3.0 |
|------|------|
| One block per category | All categories in one block |
| `product_tabs` field | `product_categories` → `orientation_tabs` |
| Top-level `color_galleries` | Per-category `color_galleries` |

### Migration Steps

1. **Backup** existing pages with v2.0 blocks
2. **Install** v3.0 plugin alongside v2.0 (different folder name)
3. **Add** new v3.0 block to page
4. **Transfer** content from each v2.0 block into v3.0 categories
5. **Delete** old v2.0 blocks once verified
6. **Deactivate** v2.0 plugin

---

## Customization

### CSS Custom Properties

Override in your theme CSS:

```css
.product-infill-options {
    --primary-tab-color: #C41E3A;    /* Active tabs */
    --header-bg-color: #2c2c54;       /* Section header */
    --inactive-tab-color: #5a5a5a;    /* Inactive tabs */
    --light-gray: #e0e0e0;            /* Secondary tab background */
    --text-dark: #333;                /* Caption text */
}
```

### Key CSS Classes

| Class | Element |
|-------|---------|
| `.category-tab-button` | Primary category tabs |
| `.orientation-tab-button` | Secondary orientation tabs |
| `.infill-section-header` | Blue header area |
| `.product-option` | Individual product card |
| `.color-swatch` | Color circle in gallery |
| `.file-badge` | Download button (CAD, PDF) |

---

## Troubleshooting

### Tabs Not Switching
- Clear browser cache
- Check for JavaScript errors in console
- Verify unique `data-category` and `data-orientation` attributes

### Colors Not Appearing
- Ensure color galleries added at category level (not orientation level)
- Check "Show Category" toggle is enabled

### File Badges Not Showing
- Verify files uploaded to WordPress Media Library
- Check file has proper URL in ACF field

### Block Not Found in Editor
- Confirm plugin activated
- Check ACF Pro is active
- Verify `block.json` has correct `acf/product-infill-options` name

---

## File Structure

```
product-infill-options-block/
├── index.php                    # Plugin registration
├── block.json                   # Block metadata
├── block.css                    # All styling
├── render.php                   # PHP template
├── acf-fields-registration.php  # ACF field definitions
└── README.md                    # This documentation
```

---

## Version History

### v3.0.0 (Current)
- Two-tier tab system (categories + orientations)
- All product categories in single block
- Per-category color galleries
- Customizable tab colors
- Horizontal scrolling category tabs
- File badge improvements

### v2.0.0
- Dynamic tab system
- Color galleries feature
- Renamed from "Rooftop Options"

### v1.0.0
- Original fixed 2-tab system

---

## Credits

Built for **PalmSHIELD Marketing Team**  
WordPress 6.1+ | ACF Pro Required | PHP 7.4+
