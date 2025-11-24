<?php
/**
 * Assets manager for Quick Order Table
 * Handles enqueuing of CSS and JavaScript files
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class QOT_Assets {

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
        // Enqueue frontend assets
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));

        // Conditionally enqueue based on shortcode presence
        add_action('wp_footer', array($this, 'maybe_enqueue_assets'), 1);
    }

    /**
     * Enqueue frontend assets
     */
    public function enqueue_frontend_assets() {
        // Register (but don't enqueue yet) CSS
        wp_register_style(
            'quick-order-table',
            QOT_PLUGIN_URL . 'assets/css/quick-order-table.css',
            array(),
            QOT_VERSION,
            'all'
        );

        // Register (but don't enqueue yet) JS
        wp_register_script(
            'quick-order-table',
            QOT_PLUGIN_URL . 'assets/js/quick-order-table.js',
            array('jquery'),
            QOT_VERSION,
            true
        );

        // Localize script with data needed for AJAX
        wp_localize_script('quick-order-table', 'qotData', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('qot-add-to-cart'),
            'messages' => array(
                'adding' => __('Đang thêm vào giỏ hàng...', 'quick-order-table'),
                'success' => __('Đã thêm sản phẩm vào giỏ hàng thành công!', 'quick-order-table'),
                'error' => __('Có lỗi xảy ra. Vui lòng thử lại.', 'quick-order-table'),
                'noItems' => __('Vui lòng chọn ít nhất một sản phẩm với số lượng lớn hơn 0.', 'quick-order-table'),
            ),
        ));
    }

    /**
     * Conditionally enqueue assets if shortcode is used
     */
    public function maybe_enqueue_assets() {
        // Check if shortcode is used on the page
        if (QOT_Shortcode::is_shortcode_used()) {
            wp_enqueue_style('quick-order-table');
            wp_enqueue_script('quick-order-table');
        }
    }

    /**
     * Force enqueue assets (used by UX Builder)
     */
    public static function force_enqueue() {
        wp_enqueue_style('quick-order-table');
        wp_enqueue_script('quick-order-table');
    }
}
