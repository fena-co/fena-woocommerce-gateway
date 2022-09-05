<?php


namespace FenaCommerceGateway;


class OrderComplete
{
    public static function title($old)
    {
        global $woocommerce;

        $orderID = self::getOrderId();

        $order = wc_get_order($orderID);

        if ($order === false) {
            error_log( 'No order found!' );
            return $old;
        }

        $paymentMethod = $order->get_payment_method();
        if ($paymentMethod != 'fena_payment') {
            return $old;
        }

        $internalStatus = $order->get_status();

        if ($internalStatus == 'cancelled') {
            return "Your payment has been rejected";
        }

        // if mark as accepted
        if (!$order->needs_payment()) {
            // Remove cart items
            $woocommerce->cart->empty_cart();
            return $old;
        } else {
            if ($order->needs_payment()) {
                $url = $order->get_cancel_order_url_raw();
                error_log('cancel url' . $url);
                if (wp_redirect($url)) {
                    exit;
                }
            }
        }
        return "Payment Rejected";
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
    static function checkOrderStatus()
    {
        $status = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : "rejected";
        if ($status == 'executed') {
            return true;
        }
        return false;
    }

    private
    static function getOrderId()
    {
        return isset($_GET['order_id']) ? sanitize_text_field($_GET['order_id']) : "0";
    }
}
