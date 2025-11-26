<?php
/**
 * Shortcode handler for Quick Order Table
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class QOT_Shortcode {

    /**
     * Single instance
     */
    private static $instance = null;

    /**
     * Flag to track if shortcode is used on page
     */
    private static $shortcode_used = false;

    /**
     * Get instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        add_shortcode('quick_order_table', array($this, 'render_shortcode'));
    }

    /**
     * Render shortcode
     *
     * @param array $atts Shortcode attributes
     * @return string HTML output
     */
    public function render_shortcode($atts) {
        // Mark that shortcode is used
        self::$shortcode_used = true;

        // Parse attributes
        $atts = shortcode_atts(array(
            'product_id' => '',
        ), $atts, 'quick_order_table');

        // Get product ID
        $product_id = $this->get_product_id($atts['product_id']);

        if (!$product_id) {
            return '<div class="qot-message qot-error">' . esc_html__('Không có sản phẩm được chỉ định.', 'quick-order-table') . '</div>';
        }

        // Get product
        $product = wc_get_product($product_id);

        if (!$product) {
            return '<div class="qot-message qot-error">' . esc_html__('Không tìm thấy sản phẩm.', 'quick-order-table') . '</div>';
        }

        // Check if product is variable
        if (!$product->is_type('variable')) {
            return ''; // Return empty string to hide table for non-variable products
        }

        // Get available variations
        $variations = $this->get_available_variations($product);

        // If no variations available, don't show the table
        if (empty($variations)) {
            return ''; // Return empty string to hide table when no variations available
        }

        // Render the table
        return $this->render_table($product, $variations);
    }

    /**
     * Get product ID from attribute or current page
     *
     * @param string $product_id Product ID from shortcode
     * @return int Product ID
     */
    private function get_product_id($product_id) {
        // If product_id is provided, use it
        if (!empty($product_id)) {
            return absint($product_id);
        }

        // Otherwise, try to get from current product page
        if (is_product()) {
            global $post;
            return $post->ID;
        }

        return 0;
    }

    /**
     * Get available variations for a product
     *
     * @param WC_Product_Variable $product Product object
     * @return array Available variations
     */
    private function get_available_variations($product) {
        $available_variations = array();

        // Get all variation IDs
        $variation_ids = $product->get_children();

        if (empty($variation_ids)) {
            return array();
        }

        foreach ($variation_ids as $variation_id) {
            $variation = wc_get_product($variation_id);

            if (!$variation || !$variation->is_purchasable() || !$variation->is_in_stock()) {
                continue;
            }

            // Get model attribute
            $attributes = $variation->get_attributes();
            $model = '';

            // Check for 'model' attribute (could be 'attribute_model' or 'attribute_pa_model')
            if (isset($attributes['model'])) {
                $model = $attributes['model'];
            } elseif (isset($attributes['attribute_model'])) {
                $model = $attributes['attribute_model'];
            } elseif (isset($attributes['pa_model'])) {
                $model = $attributes['pa_model'];
            } elseif (isset($attributes['attribute_pa_model'])) {
                $model = $attributes['attribute_pa_model'];
            }

            // Fallback to SKU if no model found
            if (empty($model)) {
                $model = $variation->get_sku() ? $variation->get_sku() : '-';
            }

            $available_variations[] = array(
                'variation_id' => $variation_id,
                'sku' => $variation->get_sku() ? $variation->get_sku() : '-',
                'model' => $model,
                'name' => $this->get_variation_name($variation),
                'description' => $this->get_variation_description($variation),
                'attributes' => $attributes,
                'price' => 'Liên hệ',
                'stock_quantity' => $variation->get_stock_quantity(),
            );
        }

        return $available_variations;
    }

    /**
     * Get variation display name
     *
     * @param WC_Product_Variation $variation Variation object
     * @return string Variation name
     */
    private function get_variation_name($variation) {
        $attributes = $variation->get_attributes();
        $name_parts = array();

        foreach ($attributes as $key => $value) {
            if (!empty($value)) {
                // Clean up attribute name (remove 'pa_' prefix if present)
                $attr_name = str_replace('pa_', '', $key);
                $attr_name = str_replace('attribute_', '', $attr_name);
                $name_parts[] = $value;
            }
        }

        $name = implode(' - ', $name_parts);

        // Fallback to variation name if no attributes found
        if (empty($name)) {
            $name = $variation->get_name();
        }

        return $name;
    }

    /**
     * Get variation description/specification
     *
     * @param WC_Product_Variation $variation Variation object
     * @return string Variation description
     */
    private function get_variation_description($variation) {
        // Try to get variation description
        $description = $variation->get_description();

        if (!empty($description)) {
            return wp_trim_words($description, 15, '...');
        }

        // Try to get custom meta field (you can customize this)
        $custom_spec = get_post_meta($variation->get_id(), '_variation_specification', true);
        if (!empty($custom_spec)) {
            return esc_html($custom_spec);
        }

        // Build from attributes as fallback
        $attributes = $variation->get_attributes();
        $spec_parts = array();

        foreach ($attributes as $key => $value) {
            if (!empty($value)) {
                $attr_label = wc_attribute_label($key);
                $spec_parts[] = $attr_label . ': ' . $value;
            }
        }

        return implode(', ', $spec_parts);
    }

    /**
     * Render the quick order table
     *
     * @param WC_Product_Variable $product Product object
     * @param array $variations Available variations
     * @return string HTML output
     */
    private function render_table($product, $variations) {
        ob_start();
        ?>
        <div class="quick-order-table-wrapper" data-product-id="<?php echo esc_attr($product->get_id()); ?>">
            <div class="qot-messages"></div>

            <div class="qot-header">
                <div class="qot-search-wrapper">
                    <label for="qot-search-input" class="qot-search-label">
                        <?php esc_html_e('Tìm kiếm', 'quick-order-table'); ?>
                    </label>
                    <div class="qot-search-controls">
                        <input type="text"
                               id="qot-search-input"
                               class="qot-search-input"
                               placeholder="<?php esc_attr_e('Nhập mã sản phẩm hoặc tên...', 'quick-order-table'); ?>">
                        <button type="button" class="qot-search-btn">
                            <?php esc_html_e('Tìm kiếm', 'quick-order-table'); ?>
                        </button>
                    </div>
                </div>
                <div class="qot-contact">
                    <span class="qot-phone-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                        </svg>
                    </span>
                    <a href="tel:0915833321" class="qot-phone-number">0915.833.321</a>
                    <button type="button" class="qot-header-add-to-cart-btn button alt">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="9" cy="21" r="1"></circle>
                            <circle cx="20" cy="21" r="1"></circle>
                            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                        </svg>
                        <?php esc_html_e('Thêm vào giỏ hàng', 'quick-order-table'); ?>
                    </button>
                </div>
            </div>

            <div class="qot-table-container">
                <table class="quick-order-table">
                    <thead>
                        <tr>
                            <th class="qot-col-sku"><?php esc_html_e('Mã sản phẩm', 'quick-order-table'); ?></th>
                            <th class="qot-col-spec"><?php esc_html_e('Quy cách phổ biến', 'quick-order-table'); ?></th>
                            <th class="qot-col-qty"><?php esc_html_e('Số lượng', 'quick-order-table'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($variations as $variation) : ?>
                            <tr class="qot-variation-row" data-variation-id="<?php echo esc_attr($variation['variation_id']); ?>">
                                <td class="qot-col-sku" data-label="<?php esc_attr_e('Mã SP', 'quick-order-table'); ?>">
                                    <span class="qot-sku"><?php echo esc_html($variation['model']); ?></span>
                                </td>
                                <td class="qot-col-spec" data-label="<?php esc_attr_e('Thông tin', 'quick-order-table'); ?>">
                                    <div class="qot-variation-name"><?php echo esc_html($product->get_name() . ' ' . $variation['name']); ?></div>
                                    <?php if (!empty($variation['description'])) : ?>
                                        <div class="qot-variation-desc"><?php echo esc_html($variation['description']); ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="qot-col-qty" data-label="<?php esc_attr_e('Số lượng', 'quick-order-table'); ?>">
                                    <input type="number"
                                           class="qot-qty-input"
                                           value="0"
                                           min="0"
                                           max="<?php echo esc_attr($variation['stock_quantity'] ?: 9999); ?>"
                                           step="1"
                                           aria-label="<?php esc_attr_e('Quantity', 'quick-order-table'); ?>">
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="qot-actions">
                <button type="button" class="qot-add-to-cart-btn button alt">
                    <?php esc_html_e('Thêm vào giỏ hàng', 'quick-order-table'); ?>
                </button>
            </div>

            <div class="qot-loading" style="display: none;">
                <span class="qot-spinner"></span>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Check if shortcode is used on current page
     */
    public static function is_shortcode_used() {
        return self::$shortcode_used;
    }

    /**
     * Mark shortcode as used (for UX Builder)
     */
    public static function mark_shortcode_used() {
        self::$shortcode_used = true;
    }
}
