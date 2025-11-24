# Quick Order Table for Variations

A standalone WordPress plugin for WooCommerce that provides a B2B-style quick order table for variable products. Allows customers to add multiple variations to cart at once with an intuitive table interface.

## Features

- ✅ Display all variations of a variable product in a clean, responsive table
- ✅ Show product SKU, specification, and quantity controls for each variation
- ✅ Add multiple variations to cart with a single click (AJAX, no page reload)
- ✅ Flatsome UX Builder integration for visual editing
- ✅ Mobile responsive design
- ✅ Secure with WordPress nonces
- ✅ Compatible with WooCommerce 8+ and PHP 7.4+

## Plugin Structure

```
quick-order-table-for-variations/
├── quick-order-table-for-variations.php  # Main plugin file
├── includes/
│   ├── class-qot-shortcode.php          # Shortcode handler
│   ├── class-qot-ajax.php               # AJAX handler
│   ├── class-qot-assets.php             # Assets manager
│   └── class-qot-ux-builder.php         # UX Builder integration
├── assets/
│   ├── js/
│   │   └── quick-order-table.js         # Frontend JavaScript
│   └── css/
│       └── quick-order-table.css        # Frontend styles
└── README.md                             # This file
```

## Requirements

- WordPress 5.8 or higher
- WooCommerce 8.0 or higher
- PHP 7.4 or higher
- Flatsome theme (optional, for UX Builder integration)

## Installation

### Method 1: Manual Installation

1. Download or clone this plugin to your WordPress plugins directory:
   ```bash
   cd wp-content/plugins/
   # If you have the files locally, copy them
   # or create the directory structure as shown above
   ```

2. Ensure all files are in place according to the plugin structure above.

3. Go to WordPress Admin → Plugins → Installed Plugins

4. Find "Quick Order Table for Variations" and click "Activate"

### Method 2: Upload as ZIP

1. Create a ZIP file of the entire plugin directory

2. Go to WordPress Admin → Plugins → Add New

3. Click "Upload Plugin" and select your ZIP file

4. Click "Install Now" then "Activate"

## Usage

### Using the Shortcode

The plugin provides a `[quick_order_table]` shortcode that you can use anywhere in your WordPress site.

#### Basic Usage (Auto-detect product on single product page):

```
[quick_order_table]
```

When used on a single product page, it automatically displays the variations for that product.

#### Specify a Product ID:

```
[quick_order_table product_id="123"]
```

Replace `123` with the actual product ID of your variable product.

#### Examples:

**In a Page/Post:**
1. Go to Pages/Posts → Add New or edit an existing page
2. Add the shortcode block or use the classic editor
3. Insert: `[quick_order_table product_id="456"]`
4. Publish/Update the page

**In a Product Description:**
1. Go to Products → Edit a variable product
2. In the product description (long or short), add: `[quick_order_table]`
3. Update the product

**In a Widget/Sidebar:**
1. Go to Appearance → Widgets
2. Add a "Custom HTML" or "Shortcode" widget
3. Insert the shortcode
4. Save

### Using with Flatsome UX Builder

If you have the Flatsome theme installed, the plugin automatically integrates with UX Builder.

#### Adding the Element:

1. **Edit a page with UX Builder:**
   - Go to Pages → Edit any page
   - Click "UX Builder" button

2. **Add the Quick Order Table element:**
   - Click the "+" button to add a new element
   - Search for "Quick Order Table" in the WooCommerce category
   - Click to add it to your page

3. **Configure the element:**
   - **Product ID field:** Enter a specific product ID, or leave empty to use the current product (on single product pages)
   - **Or Select Product:** Use the dropdown to search and select a variable product

4. **Preview and publish:**
   - The table will show a preview in the builder
   - Click "Publish" or "Update" to save your changes

#### UX Builder Tips:

- Use on single product pages: Leave product ID empty to automatically use the current product
- Use on any page: Enter a specific product ID or use the product selector
- The element shows a helpful info message in the builder indicating which product is being displayed

## Customization

### Custom Variation Specifications

By default, the plugin displays variation descriptions or builds specifications from attributes. You can add custom specifications using a custom field:

1. Go to Products → Edit a variation
2. In the variation details, add a custom field: `_variation_specification`
3. Enter your custom specification text
4. The plugin will display this text in the "Quy cách phổ biến" column

### Styling

The plugin includes default CSS that works with most themes. To customize:

1. **Add custom CSS via Customizer:**
   - Go to Appearance → Customize → Additional CSS
   - Add your custom styles targeting `.quick-order-table` classes

2. **Override in your theme:**
   - Copy `assets/css/quick-order-table.css` to your child theme
   - Dequeue the plugin's CSS and enqueue your custom version:
   ```php
   add_action('wp_enqueue_scripts', 'my_custom_qot_styles', 100);
   function my_custom_qot_styles() {
       wp_dequeue_style('quick-order-table');
       wp_enqueue_style('my-quick-order-table', get_stylesheet_directory_uri() . '/css/quick-order-table.css');
   }
   ```

### JavaScript Customization

To extend functionality, you can hook into the JavaScript events:

```javascript
jQuery(document).on('qot_before_add_to_cart', function(event, items) {
    // Runs before adding to cart
    console.log('Adding items:', items);
});

jQuery(document).on('qot_added_to_cart', function(event, response) {
    // Runs after successful cart addition
    console.log('Cart updated:', response);
});
```

## Translations

The plugin is translation-ready. Text domain: `quick-order-table`

To translate:

1. Use a plugin like Loco Translate or Poedit
2. Create translations for the `quick-order-table` text domain
3. Save `.po` and `.mo` files to the `languages` directory

## Troubleshooting

### Plugin doesn't appear or activate
- **Check WooCommerce:** Ensure WooCommerce is installed and activated
- **Check PHP version:** Verify you're running PHP 7.4 or higher

### Shortcode shows "Product not found"
- **Verify product ID:** Make sure the product ID exists and is published
- **Check product type:** The product must be a "Variable Product" with variations

### Shortcode shows "No variations available"
- **Check variations:** Ensure the product has published variations
- **Check stock status:** Variations must be in stock and purchasable
- **Verify variation settings:** Go to the product and check each variation is properly configured

### AJAX not working
- **Check JavaScript console:** Open browser developer tools (F12) and check for errors
- **Verify AJAX URL:** The plugin uses WordPress AJAX URL, ensure it's not blocked
- **Check nonce:** Clear cache and try again

### Styles not loading
- **Clear cache:** Clear any caching plugins and browser cache
- **Check theme conflicts:** Try switching to a default WordPress theme temporarily
- **Check file permissions:** Ensure `assets/css/quick-order-table.css` is readable

### UX Builder element not showing
- **Verify Flatsome:** Ensure Flatsome theme is active
- **Check version:** Update to the latest Flatsome version
- **Refresh builder:** Clear UX Builder cache in Flatsome → Advanced → Clear Cache

## Frequently Asked Questions

**Q: Can I use this with simple products?**
A: No, this plugin is specifically designed for variable products with variations.

**Q: Does it work with other themes besides Flatsome?**
A: Yes! The shortcode works with any WordPress theme. The UX Builder integration is a bonus feature for Flatsome users.

**Q: Can customers add multiple variations at once?**
A: Yes! That's the main feature. Customers can set quantities for multiple variations and add them all to cart with one click.

**Q: Is it mobile responsive?**
A: Yes, the table is fully responsive and adapts to mobile devices.

**Q: Does it update the mini cart?**
A: Yes, the plugin uses WooCommerce AJAX fragments to update the mini cart automatically.

**Q: Can I customize the table columns?**
A: Currently, the columns are fixed (SKU, Specification, Quantity). For custom columns, you would need to modify the plugin code.

## Changelog

### Version 1.0.0
- Initial release
- Shortcode support for variable products
- AJAX add to cart functionality
- Flatsome UX Builder integration
- Responsive design
- Translation ready

## Support

For issues, questions, or feature requests, please contact the plugin author or create an issue in your version control system.

## License

This plugin is licensed under GPL v2 or later.

## Credits

Developed for WooCommerce and WordPress compatibility.
Flatsome theme integration included for enhanced user experience.
