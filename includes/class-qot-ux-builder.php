<?php
/**
 * Flatsome UX Builder integration for Quick Order Table
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class QOT_UX_Builder {

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
        // Register UX Builder element
        add_action('ux_builder_setup', array($this, 'register_ux_builder_element'));
    }

    /**
     * Register element with UX Builder
     */
    public function register_ux_builder_element() {
        // Register the element
        add_ux_builder_shortcode('quick_order_table', array(
            'name' => __('Quick Order Table', 'quick-order-table'),
            'category' => __('WooCommerce', 'quick-order-table'),
            'toolbar_thumbnail' => 'dashicons-list-view',
            'info' => __('{{ product_id }}', 'quick-order-table'),
            'wrap' => false,
            'inline' => false,
            'options' => array(
                'product_id' => array(
                    'type' => 'textfield',
                    'heading' => __('Product ID', 'quick-order-table'),
                    'description' => __('Enter the variable product ID. Leave empty to use the current product on single product pages.', 'quick-order-table'),
                    'default' => '',
                ),
                'product_selector' => array(
                    'type' => 'select',
                    'heading' => __('Or Select Product', 'quick-order-table'),
                    'description' => __('Search and select a variable product.', 'quick-order-table'),
                    'config' => array(
                        'placeholder' => __('Search for a product...', 'quick-order-table'),
                        'postSelect' => array(
                            'post_type' => array('product'),
                            'post_status' => array('publish'),
                        ),
                    ),
                    'default' => '',
                ),
            ),
            'presets' => array(
                array(
                    'name' => __('Default', 'quick-order-table'),
                    'content' => '[quick_order_table]',
                ),
            ),
        ));

        // Add custom rendering callback
        add_filter('ux_builder_render_quick_order_table', array($this, 'render_for_builder'), 10, 2);
    }

    /**
     * Render element for UX Builder
     *
     * @param string $content Shortcode content
     * @param array $atts Shortcode attributes
     * @return string Rendered output
     */
    public function render_for_builder($content, $atts) {
        // Mark shortcode as used to ensure assets are enqueued
        QOT_Shortcode::mark_shortcode_used();

        // Force enqueue assets for builder
        QOT_Assets::force_enqueue();

        // If product_selector is set, use it as product_id
        if (!empty($atts['product_selector'])) {
            $atts['product_id'] = $atts['product_selector'];
        }

        // Get the shortcode instance
        $shortcode = QOT_Shortcode::get_instance();

        // Render the shortcode
        $output = $shortcode->render_shortcode($atts);

        // In builder mode, wrap with a helpful message
        if (function_exists('ux_builder_is_active') && ux_builder_is_active()) {
            $product_id = !empty($atts['product_id']) ? $atts['product_id'] : 'auto';
            $output = '<div class="qot-builder-wrapper">' .
                     '<div class="qot-builder-info">' .
                     sprintf(__('Quick Order Table (Product ID: %s)', 'quick-order-table'), esc_html($product_id)) .
                     '</div>' .
                     $output .
                     '</div>';
        }

        return $output;
    }

    /**
     * Get products for select dropdown (AJAX callback)
     */
    public function get_products_ajax() {
        check_ajax_referer('ux-builder-admin', 'security');

        $search = isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '';

        $args = array(
            'post_type' => 'product',
            'post_status' => 'publish',
            'posts_per_page' => 20,
            's' => $search,
            'tax_query' => array(
                array(
                    'taxonomy' => 'product_type',
                    'field' => 'slug',
                    'terms' => 'variable',
                ),
            ),
        );

        $query = new WP_Query($args);
        $results = array();

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $results[] = array(
                    'id' => get_the_ID(),
                    'text' => get_the_title() . ' (ID: ' . get_the_ID() . ')',
                );
            }
            wp_reset_postdata();
        }

        wp_send_json($results);
    }
}
