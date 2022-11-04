<?php

namespace FenaCommerceGateway;

use WC_Payment_Gateway;

final class FenaPaymentGateway extends WC_Payment_Gateway
{
    private $terminal_id;
    private $terminal_secret;

    public function __construct()
    {
        $this->id = 'fena_payment';
        $this->method_title = 'Fena';

        $this->method_description = "Fast instant bank to bank payments";  // to backend
        $this->order_button_text = 'Proceed to pay';

        $this->title = $this->get_option('title');
        $this->description = $this->get_option('description');

        $this->has_fields = false;

        // only support products
        $this->supports = array(
            'products'
        );

        $this->countries = ['GB'];

        $this->init_form_fields();
        $this->init_settings();

        $this->enabled = $this->get_option('enabled');
        $this->terminal_id = $this->get_option('terminal_id');
        $this->terminal_secret = $this->get_option('terminal_secret');

        add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));

        add_action('woocommerce_api_fena', array($this, 'webhook'));

        add_action('template_redirect', 'order_received_custom_payment_redirect');
    }

    public function init_form_fields()
    {
        $this->form_fields = AdminPortalOptions::get();
    }

    function admin_options()
    {
        AdminPortalUI::get($this->generate_settings_html([], false));
    }

    public function process_admin_options()
    {
        parent::process_admin_options();
        return AdminPortalOptions::validate($this->terminal_secret, $this->terminal_id);
    }

    public function process_payment($order_id)
    {
        return PaymentProcess::process($order_id, $this->terminal_id, $this->terminal_secret);
    }

    public function get_icon()
    {
        return CheckoutIcon::get($this->id);
    }

    public function webhook()
    {
        PaymentNotification::process($this->terminal_id, $this->terminal_secret);
    }

    public function order_received_custom_payment_redirect()
    {

        // do nothing if we are not on the order received page and not coming from Fena payment flow
        if (!is_wc_endpoint_url('order-received') || empty($_GET['order_id'])) {
            return;
        }

        // error_log('Order received endpoint');

        $order_number = self::getOrderNumber();
        $status = self::getOrderStatus();

        $args = array(
            'post_type' => 'shop_order',
            'post_status' => 'any',
            'meta_query' => array(
                array(
                    'key' => '_alg_wc_full_custom_order_number',
                    'value' => $order_number,  //here you pass the Order Number
                    'compare' => '=',
                )
            )
        );
        $query = new \WP_Query($args);
        if (!empty($query->posts)) {
            $orderId = $query->posts[0]->ID;
        } else {
            $args = array(
                'post_type' => 'shop_order',
                'post_status' => 'any',
                'meta_query' => array(
                    array(
                        'key' => '_order_number',
                        'value' => $order_number,  //here you pass the Order Number
                        'compare' => '=',
                    )
                )
            );
            $query = new \WP_Query($args);
            if (!empty($query->posts)) {
                $orderId = $query->posts[0]->ID;
            }
        }

        if (!isset($orderId)) {
            error_log("Order ID not found");
            die();
        }

        $order = wc_get_order($orderId);

        if ($order === false) {
            error_log('No order found!');
            return;
        }

        $paymentMethod = $order->get_payment_method();
        if ($paymentMethod === 'fena_payment') {
            // if cash of delivery, redirecto to a custom thank you page
            wp_safe_redirect($order->get_checkout_order_received_url());
            exit; // always exit
        }
    }

    private
    static function getOrderStatus()
    {
        return isset($_GET['status']) ? sanitize_text_field($_GET['status']) : "rejected";
    }

    private
    static function getOrderNumber()
    {
        return isset($_GET['order_id']) ? sanitize_text_field($_GET['order_id']) : "0";
    }
}
