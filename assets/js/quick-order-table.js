/**
 * Quick Order Table - Frontend JavaScript
 * Handles quantity controls and AJAX cart operations
 */

(function($) {
    'use strict';

    /**
     * Quick Order Table Handler
     */
    var QuickOrderTable = {
        /**
         * Initialize
         */
        init: function() {
            this.bindEvents();
        },

        /**
         * Bind event handlers
         */
        bindEvents: function() {
            var self = this;

            // Validate quantity input
            $(document).on('change', '.qot-qty-input', function() {
                var $input = $(this);
                var value = parseInt($input.val());
                var min = parseInt($input.attr('min')) || 0;
                var max = parseInt($input.attr('max')) || 9999;

                if (isNaN(value) || value < min) {
                    $input.val(min);
                } else if (value > max) {
                    $input.val(max);
                }
            });

            // Add to cart button
            $(document).on('click', '.qot-add-to-cart-btn', function(e) {
                e.preventDefault();
                self.addToCart($(this));
            });
        },

        /**
         * Add selected items to cart
         */
        addToCart: function($btn) {
            var self = this;
            var $wrapper = $btn.closest('.quick-order-table-wrapper');
            var $table = $wrapper.find('.quick-order-table');
            var productId = $wrapper.data('product-id');

            // Collect items with quantity > 0
            var items = [];
            $table.find('.qot-variation-row').each(function() {
                var $row = $(this);
                var variationId = $row.data('variation-id');
                var quantity = parseInt($row.find('.qot-qty-input').val()) || 0;

                if (quantity > 0) {
                    items.push({
                        variation_id: variationId,
                        quantity: quantity
                    });
                }
            });

            // Check if any items selected
            if (items.length === 0) {
                self.showMessage($wrapper, 'error', qotData.messages.noItems);
                return;
            }

            // Show loading state
            self.setLoadingState($wrapper, true);
            $btn.prop('disabled', true);

            // Send AJAX request
            $.ajax({
                url: qotData.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'quick_order_add_to_cart',
                    nonce: qotData.nonce,
                    product_id: productId,
                    items: items
                },
                success: function(response) {
                    if (response.success) {
                        // Show success message
                        self.showMessage($wrapper, 'success', response.data.message);

                        // Reset all quantity inputs to 0
                        $table.find('.qot-qty-input').val(0);

                        // Trigger WooCommerce cart update event
                        $(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, $btn]);

                        // Update mini cart if fragments are available
                        if (response.fragments) {
                            $.each(response.fragments, function(key, value) {
                                $(key).replaceWith(value);
                            });
                        }

                        // Scroll to message
                        self.scrollToMessage($wrapper);
                    } else {
                        // Show error message
                        var errorMsg = response.data && response.data.message ?
                            response.data.message : qotData.messages.error;
                        self.showMessage($wrapper, 'error', errorMsg);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Quick Order Table Error:', error);
                    self.showMessage($wrapper, 'error', qotData.messages.error);
                },
                complete: function() {
                    // Remove loading state
                    self.setLoadingState($wrapper, false);
                    $btn.prop('disabled', false);
                }
            });
        },

        /**
         * Show message
         */
        showMessage: function($wrapper, type, message) {
            var $messagesContainer = $wrapper.find('.qot-messages');
            var typeClass = type === 'success' ? 'qot-success' : 'qot-error';

            var $message = $('<div class="qot-message ' + typeClass + '">' + message + '</div>');

            // Clear existing messages
            $messagesContainer.empty();

            // Add new message
            $messagesContainer.append($message);

            // Auto-hide success messages after 5 seconds
            if (type === 'success') {
                setTimeout(function() {
                    $message.fadeOut(400, function() {
                        $(this).remove();
                    });
                }, 5000);
            }
        },

        /**
         * Set loading state
         */
        setLoadingState: function($wrapper, loading) {
            var $loading = $wrapper.find('.qot-loading');
            var $table = $wrapper.find('.qot-table-container');

            if (loading) {
                $loading.show();
                $table.css('opacity', '0.5');
            } else {
                $loading.hide();
                $table.css('opacity', '1');
            }
        },

        /**
         * Scroll to message
         */
        scrollToMessage: function($wrapper) {
            var $messagesContainer = $wrapper.find('.qot-messages');
            if ($messagesContainer.length && $messagesContainer.children().length) {
                $('html, body').animate({
                    scrollTop: $messagesContainer.offset().top - 100
                }, 500);
            }
        }
    };

    /**
     * Initialize on document ready
     */
    $(document).ready(function() {
        QuickOrderTable.init();
    });

})(jQuery);
