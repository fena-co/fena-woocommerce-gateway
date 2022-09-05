<?php


namespace FenaCommerceGateway;


class OrderComplete
{
    public static function title($old)
    {
        global $woocommerce;

        $orderID = self::getOrderId();
        $status = self::getOrderStatus();

        $order = wc_get_order($orderID);

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
    static function getOrderId()
    {
        return isset($_GET['order_id']) ? sanitize_text_field($_GET['order_id']) : "0";
    }
}
