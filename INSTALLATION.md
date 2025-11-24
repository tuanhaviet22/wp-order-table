# Quick Order Table for Variations - Installation Guide

Complete installation and setup instructions for the Quick Order Table plugin.

---

## 📋 Prerequisites

Before installing, ensure you have:

- ✅ WordPress 5.8 or higher
- ✅ WooCommerce 8.0 or higher
- ✅ PHP 7.4 or higher
- ✅ At least one variable product with variations

**Optional:**
- Flatsome theme (for UX Builder integration)

---

## 🚀 Installation Methods

### Method 1: Upload ZIP via WordPress Admin (Recommended)

1. **Download the Plugin**
   - Download `quick-order-table-for-variations.zip`

2. **Login to WordPress**
   - Go to your WordPress admin panel
   - URL: `https://yoursite.com/wp-admin`

3. **Upload Plugin**
   - Navigate to: **Plugins → Add New**
   - Click **"Upload Plugin"** button at the top
   - Click **"Choose File"** and select the ZIP file
   - Click **"Install Now"**

4. **Activate Plugin**
   - After installation, click **"Activate Plugin"**
   - You'll see a success message

5. **Verify Installation**
   - Go to **Plugins → Installed Plugins**
   - Look for "Quick Order Table for Variations"
   - Status should show "Active"

---

### Method 2: Manual Upload via FTP

1. **Extract ZIP File**
   - Extract `quick-order-table-for-variations.zip`
   - You should see a folder: `quick-order-table-for-variations/`

2. **Upload via FTP**
   - Connect to your server using FTP client (FileZilla, etc.)
   - Navigate to: `/wp-content/plugins/`
   - Upload the entire `quick-order-table-for-variations/` folder

3. **Activate Plugin**
   - Go to WordPress admin: **Plugins → Installed Plugins**
   - Find "Quick Order Table for Variations"
   - Click **"Activate"**

---

### Method 3: WP-CLI Installation

```bash
# Upload ZIP to server first, then:
wp plugin install /path/to/quick-order-table-for-variations.zip --activate

# Or if folder is already in plugins directory:
wp plugin activate quick-order-table-for-variations
```

---

## ✅ Post-Installation Setup

### 1. Verify WooCommerce Dependency

After activation, if you see this message:
```
Quick Order Table for Variations requires WooCommerce to be installed and active.
```

**Solution:**
- Go to **Plugins → Add New**
- Search for "WooCommerce"
- Install and activate WooCommerce
- The error will disappear

### 2. Check Plugin is Working

**Quick Test:**
1. Go to **Pages → Add New**
2. Add this shortcode: `[quick_order_table product_id="YOUR_PRODUCT_ID"]`
3. Preview the page
4. You should see the quick order table

---

## 🎯 Usage Guide

### Using Shortcodes

#### Option 1: Auto-detect Product (Single Product Pages)

Add to your product page content:
```
[quick_order_table]
```

This automatically detects the current product.

#### Option 2: Specify Product ID

Use on any page/post:
```
[quick_order_table product_id="123"]
```

Replace `123` with your variable product ID.

**Finding Product ID:**
1. Go to **Products → All Products**
2. Hover over a product name
3. Look at the browser status bar: `...post.php?post=123...`
4. The number is your product ID

---

### Using with Flatsome Theme (UX Builder)

If you have Flatsome theme installed:

1. **Edit a Page with UX Builder**
   - Go to any page
   - Click **"UX Builder"** button

2. **Add the Element**
   - Click the **"+"** button to add an element
   - Search for **"Quick Order Table"**
   - It's in the **"WooCommerce"** category
   - Click to add

3. **Configure Element**
   - **Product ID:** Leave empty for current product, or enter specific ID
   - **Or Select Product:** Use dropdown to search/select a product
   - Click **"Save"**

4. **Publish**
   - Click **"Publish"** in UX Builder
   - View the page to see your table

---

### Adding to Widget Areas

1. Go to **Appearance → Widgets**
2. Find the widget area (sidebar, footer, etc.)
3. Add a **"Custom HTML"** or **"Shortcode"** widget
4. Enter: `[quick_order_table product_id="123"]`
5. Save

---

### Adding to Template Files

In your theme template files (requires PHP knowledge):

```php
<?php echo do_shortcode('[quick_order_table product_id="123"]'); ?>
```

Or auto-detect on single product pages:
```php
<?php
if (is_product()) {
    echo do_shortcode('[quick_order_table]');
}
?>
```

---

## 🎨 Customization

### Customize Phone Number

Edit: `includes/class-qot-shortcode.php`

Find line ~240:
```php
<a href="tel:0915833321" class="qot-phone-number">0915.833.321</a>
```

Change to your phone number:
```php
<a href="tel:0123456789" class="qot-phone-number">012.345.6789</a>
```

### Custom CSS Styling

1. Go to **Appearance → Customize**
2. Click **"Additional CSS"**
3. Add your custom styles:

```css
/* Change header background */
.qot-header {
    background: #your-color;
}

/* Change button color */
.qot-search-btn {
    background: #your-color;
}

/* Change table header */
.quick-order-table thead {
    background: #your-color;
}
```

### Modify Search Placeholder

Edit: `includes/class-qot-shortcode.php`

Find line ~228:
```php
placeholder="<?php esc_attr_e('Nhập mã sản phẩm hoặc tên...', 'quick-order-table'); ?>"
```

Change to:
```php
placeholder="Your custom text..."
```

---

## 🔧 Troubleshooting

### Issue: Plugin Not Showing in Menu

**Solution:**
- Check that WooCommerce is active
- Go to **Plugins → Installed Plugins**
- Ensure "Quick Order Table for Variations" status is "Active"

### Issue: Shortcode Shows as Text

**Symptoms:**
```
[quick_order_table product_id="123"]
```
Shows literally on the page.

**Solutions:**
1. Make sure you're using the **Text** or **Code** editor, not Visual editor
2. In Gutenberg, use a **"Shortcode"** block, not a paragraph block
3. Check that the plugin is activated

### Issue: "No Product Specified" Error

**Solution:**
- When using `[quick_order_table]` without product_id, ensure you're on a single product page
- Or add a product_id: `[quick_order_table product_id="123"]`

### Issue: "Product Not Found" Error

**Solution:**
- Verify the product ID exists
- Check that the product is published (not draft/trashed)

### Issue: "This Product Does Not Have Variations"

**Solution:**
- The plugin only works with **Variable Products**
- Go to **Products → Edit Product**
- Under **Product Data**, select **"Variable product"**
- Add attributes and variations

### Issue: "No Variations Available"

**Solution:**
- Check that variations are published
- Verify variations have stock
- Ensure variations are marked as purchasable

### Issue: AJAX Error When Adding to Cart

**Check:**
1. Browser console (F12) for JavaScript errors
2. PHP error log for server-side errors
3. Ensure WooCommerce cart functionality works normally

**Common Fixes:**
- Clear browser cache and WordPress cache
- Deactivate conflicting plugins
- Switch to default WordPress theme to test
- Check `.htaccess` for rewrite issues

### Issue: Search Not Working

**Solution:**
- Clear browser cache (Ctrl+Shift+Delete)
- Check browser console (F12) for errors
- Ensure JavaScript is not blocked

### Issue: Mini Cart Not Updating

**Check:**
- Your theme supports WooCommerce cart fragments
- Check browser console for AJAX errors
- Test with a default WooCommerce theme (Storefront)

---

## 🧪 Testing Checklist

After installation, test these features:

- [ ] Shortcode displays table correctly
- [ ] Search filters products in real-time
- [ ] Quantity inputs work
- [ ] Add to cart button adds products
- [ ] Success message displays
- [ ] Mini cart updates with new items
- [ ] Cart page shows correct products
- [ ] Mobile responsive layout works
- [ ] Phone number is clickable
- [ ] UX Builder element works (if Flatsome)

---

## 📊 File Structure

After installation, you should have:

```
wp-content/plugins/quick-order-table-for-variations/
├── quick-order-table-for-variations.php  (Main file)
├── includes/
│   ├── class-qot-shortcode.php
│   ├── class-qot-ajax.php
│   ├── class-qot-assets.php
│   └── class-qot-ux-builder.php
├── assets/
│   ├── js/
│   │   └── quick-order-table.js
│   └── css/
│       └── quick-order-table.css
├── README.md
├── readme.txt
├── INSTALLATION.md
└── AUDIT-REPORT.md
```

---

## 🔄 Updating the Plugin

### Manual Update

1. Deactivate the current version
2. Delete the old plugin folder via FTP
3. Upload the new version
4. Activate the plugin
5. Clear all caches

### Automatic Update (if on WordPress.org)

1. Go to **Dashboard → Updates**
2. Check for plugin updates
3. Click **"Update Now"**

**Note:** Always backup your site before updating!

---

## 🗑️ Uninstallation

### Remove Plugin

1. **Deactivate First**
   - Go to **Plugins → Installed Plugins**
   - Click **"Deactivate"** under plugin name

2. **Delete Plugin**
   - After deactivation, click **"Delete"**
   - Confirm deletion

3. **Clean Up (Optional)**
   - Shortcodes on pages will no longer work
   - Remove `[quick_order_table]` from your pages manually

### What Gets Removed

- Plugin files from `/wp-content/plugins/`
- That's it! No database entries are created

### What Stays

- Your products and variations (unchanged)
- WooCommerce cart functionality (unchanged)
- Any custom CSS you added (stays in your theme)

---

## 🔐 Security Notes

- ✅ Plugin uses WordPress nonces for security
- ✅ All input is sanitized and validated
- ✅ Output is properly escaped
- ✅ No sensitive data stored
- ✅ Compatible with security plugins

---

## 📞 Support

Need help with installation?

**Contact:** 0915.833.321

**Before Contacting:**
1. Check this installation guide
2. Review troubleshooting section
3. Check WordPress error logs
4. Try with default theme/no plugins

**Provide When Asking for Help:**
- WordPress version
- WooCommerce version
- PHP version
- Theme name and version
- Error messages (exact text)
- Steps to reproduce the issue

---

## ✨ Next Steps

After successful installation:

1. ✅ Create or edit a variable product
2. ✅ Add multiple variations
3. ✅ Insert shortcode on a page
4. ✅ Test the quick order functionality
5. ✅ Customize styling to match your brand
6. ✅ Add to your product pages

---

## 📚 Additional Resources

- **README.md** - Feature overview and usage
- **AUDIT-REPORT.md** - Security and code quality audit
- **readme.txt** - WordPress.org formatted readme

---

**Congratulations!** 🎉

Your Quick Order Table for Variations plugin is now installed and ready to use!
