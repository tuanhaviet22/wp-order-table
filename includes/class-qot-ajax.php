<?php
/**
 * AJAX handler for Quick Order Table
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class QOT_Ajax {

    /**
     * Single instance
     */
    private static $instance = null;

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
        // Register AJAX handlers for logged-in users
        add_action('wp_ajax_quick_order_add_to_cart', array($this, 'add_to_cart'));

        // Register AJAX handlers for non-logged-in users
        add_action('wp_ajax_nopriv_quick_order_add_to_cart', array($this, 'add_to_cart'));
    }

    /**
     * Add multiple variations to cart via AJAX
     */
    public function add_to_cart() {
        try {
            // Verify nonce
            if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'qot-add-to-cart')) {
                throw new Exception(__('Kiểm tra bảo mật thất bại.', 'quick-order-table'));
            }

            // Get product ID
            if (!isset($_POST['product_id']) || empty($_POST['product_id'])) {
                throw new Exception(__('Mã sản phẩm là bắt buộc.', 'quick-order-table'));
            }

            $product_id = absint($_POST['product_id']);

            // Validate product
            $product = wc_get_product($product_id);
            if (!$product || !$product->is_type('variable')) {
                throw new Exception(__('Sản phẩm không hợp lệ.', 'quick-order-table'));
            }

            // Get items
            if (!isset($_POST['items']) || !is_array($_POST['items']) || empty($_POST['items'])) {
                throw new Exception(__('Không có sản phẩm nào được chọn.', 'quick-order-table'));
            }

            $items = $_POST['items'];
            $added_count = 0;
            $failed_items = array();

            // Get valid variation IDs for this product
            $valid_variation_ids = $product->get_children();

            // Process each item
            foreach ($items as $item) {
                if (!isset($item['variation_id']) || !isset($item['quantity'])) {
                    continue;
                }

                $variation_id = absint($item['variation_id']);
                $quantity = absint($item['quantity']);

                // Skip if quantity is 0
                if ($quantity <= 0) {
                    continue;
                }

                // Validate that variation belongs to this product
                if (!in_array($variation_id, $valid_variation_ids)) {
                    $failed_items[] = array(
                        'variation_id' => $variation_id,
                        'message' => __('Biến thể không hợp lệ.', 'quick-order-table'),
                    );
                    continue;
                }

                // Get variation product
                $variation = wc_get_product($variation_id);
                if (!$variation) {
                    $failed_items[] = array(
                        'variation_id' => $variation_id,
                        'message' => __('Không tìm thấy biến thể.', 'quick-order-table'),
                    );
                    continue;
                }

                // Check if variation is purchasable
                if (!$variation->is_purchasable()) {
                    $failed_items[] = array(
                        'variation_id' => $variation_id,
                        'message' => __('Biến thể này không thể mua.', 'quick-order-table'),
                    );
                    continue;
                }

                // Check stock
                if (!$variation->is_in_stock()) {
                    $failed_items[] = array(
                        'variation_id' => $variation_id,
                        'message' => __('Hết hàng.', 'quick-order-table'),
                    );
                    continue;
                }

                // Check stock quantity
                if ($variation->managing_stock()) {
                    $stock_quantity = $variation->get_stock_quantity();
                    if ($stock_quantity !== null && $quantity > $stock_quantity) {
                        $failed_items[] = array(
                            'variation_id' => $variation_id,
                            'message' => sprintf(__('Chỉ còn %d sản phẩm trong kho.', 'quick-order-table'), $stock_quantity),
                        );
                        continue;
                    }
                }

                // Get variation attributes
                $variation_attributes = $variation->get_attributes();

                // Add to cart
                $cart_item_key = WC()->cart->add_to_cart(
                    $product_id,
                    $quantity,
                    $variation_id,
                    $variation_attributes
                );

                if ($cart_item_key) {
                    $added_count++;
                } else {
                    $failed_items[] = array(
                        'variation_id' => $variation_id,
                        'message' => __('Không thể thêm vào giỏ hàng.', 'quick-order-table'),
                    );
                }
            }

            // Build response message
            if ($added_count > 0) {
                $message = sprintf(
                    _n(
                        'Đã thêm %d sản phẩm vào giỏ hàng.',
                        'Đã thêm %d sản phẩm vào giỏ hàng.',
                        $added_count,
                        'quick-order-table'
                    ),
                    $added_count
                );

                if (!empty($failed_items)) {
                    $message .= ' ' . sprintf(
                        _n(
                            '%d sản phẩm thất bại.',
                            '%d sản phẩm thất bại.',
                            count($failed_items),
                            'quick-order-table'
                        ),
                        count($failed_items)
                    );
                }
            } else {
                throw new Exception(__('Không thể thêm sản phẩm vào giỏ hàng.', 'quick-order-table'));
            }

            // Get cart fragments for mini cart update
            WC_AJAX::get_refreshed_fragments();

            // Send success response
            wp_send_json_success(array(
                'message' => $message,
                'added_count' => $added_count,
                'failed_count' => count($failed_items),
                'failed_items' => $failed_items,
            ));

        } catch (Exception $e) {
            wp_send_json_error(array(
                'message' => $e->getMessage(),
            ));
        }
    }
}
