<?php
/**
 * Plugin Name: Quick Order Table for Variations
 * Plugin URI: https://example.com/quick-order-table
 * Description: A B2B-style quick order table for WooCommerce variable products. Allows customers to add multiple variations to cart at once. Includes Flatsome UX Builder support.
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://example.com
 * Text Domain: quick-order-table
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * WC requires at least: 8.0
 * WC tested up to: 9.0
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('QOT_VERSION', '1.0.0');
define('QOT_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('QOT_PLUGIN_URL', plugin_dir_url(__FILE__));
define('QOT_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Main plugin class
 */
class Quick_Order_Table_For_Variations {

    /**
     * Single instance of the class
     */
    private static $instance = null;

    /**
     * Get single instance
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
        // Check if WooCommerce is active
        add_action('plugins_loaded', array($this, 'init'));
    }

    /**
     * Initialize plugin
     */
    public function init() {
        // Check WooCommerce dependency
        if (!class_exists('WooCommerce')) {
            add_action('admin_notices', array($this, 'woocommerce_missing_notice'));
            return;
        }

        // Load plugin text domain
        load_plugin_textdomain('quick-order-table', false, dirname(QOT_PLUGIN_BASENAME) . '/languages');

        // Include required files
        $this->include_files();

        // Initialize components
        $this->init_components();
    }

    /**
     * Include required files
     */
    private function include_files() {
        require_once QOT_PLUGIN_DIR . 'includes/class-qot-shortcode.php';
        require_once QOT_PLUGIN_DIR . 'includes/class-qot-ajax.php';
        require_once QOT_PLUGIN_DIR . 'includes/class-qot-assets.php';

        // Include Flatsome UX Builder integration if Flatsome is active
        if (function_exists('ux_builder_setup')) {
            require_once QOT_PLUGIN_DIR . 'includes/class-qot-ux-builder.php';
        }
    }

    /**
     * Initialize plugin components
     */
    private function init_components() {
        // Initialize shortcode handler
        QOT_Shortcode::get_instance();

        // Initialize AJAX handler
        QOT_Ajax::get_instance();

        // Initialize assets manager
        QOT_Assets::get_instance();

        // Initialize UX Builder integration if available
        if (class_exists('QOT_UX_Builder')) {
            QOT_UX_Builder::get_instance();
        }
    }

    /**
     * WooCommerce missing notice
     */
    public function woocommerce_missing_notice() {
        ?>
        <div class="error">
            <p><?php esc_html_e('Quick Order Table for Variations requires WooCommerce to be installed and active.', 'quick-order-table'); ?></p>
        </div>
        <?php
    }
}

/**
 * Initialize the plugin
 */
function qot_init() {
    return Quick_Order_Table_For_Variations::get_instance();
}

// Start the plugin
qot_init();
