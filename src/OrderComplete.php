<?php


namespace FenaCommerceGateway;


class OrderComplete
{
    public static function title($old)
    {
        global $woocommerce;

        $order_number = self::getOrderNumber();
        $status = self::getOrderStatus();

        $args    = array(
            'post_type'      => 'shop_order',
            'post_status'    => 'any',
            'meta_query'     => array(
                array(
                    'key'        => '_alg_wc_full_custom_order_number',
                    'value'      => $order_number,  //here you pass the Order Number
                    'compare'    => '=',
                )
            )
        );
        $query   = new \WP_Query( $args );
        if ( !empty( $query->posts ) ) {
            $orderId = $query->posts[ 0 ]->ID;
        } else {
            $args    = array(
                'post_type'      => 'shop_order',
                'post_status'    => 'any',
                'meta_query'     => array(
                    array(
                        'key'        => '_order_number',
                        'value'      => $order_number,  //here you pass the Order Number
                        'compare'    => '=',
                    )
                )
            );
            $query   = new \WP_Query( $args );
            if ( !empty( $query->posts ) ) {
                $orderId = $query->posts[ 0 ]->ID;
            }
        }

        if (!isset($orderId)) {
            error_log( "Order ID not found" );
            die();
        }

        $order = wc_get_order($orderId);

        if ($order === false) {
            error_log( 'No order found!' );
            return $old;
        }

        $paymentMethod = $order->get_payment_method();
        if ($paymentMethod != 'fena_payment') {
            return $old;
        }

        if ($status == 'rejected') {
            return "Your payment has been cancelled";
        }

        return $old;
    }

    public
    static function text($old)
    {
        if (self::checkOrderStatus()) {
            return $old;
        }
        return "Unfortunately your payment has been rejected.";
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
