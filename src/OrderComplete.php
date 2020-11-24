<?php


namespace FenaCommerceGateway;


class OrderComplete
{
    public static function title($old, FenaPaymentGateway $faizPayPaymentGateway)
    {
        global $woocommerce;

        $orderID = self::getOrderId();

        $order = wc_get_order($orderID);

        if ($order === false) {
            return $old;
        }

        $paymentMethod = $order->get_payment_method();
        if ($paymentMethod != 'faizpay_payment') {
            return $old;
        }

        // if mark as accepted
        if (self::checkOrderStatus()) {
            // if doesn't need payment
            if (!$order->needs_payment()) {
                // Remove cart items
                $woocommerce->cart->empty_cart();
                // only show the final link if order completed in last half an hour
                $datePaid = $order->get_date_paid();
                if ($datePaid instanceof \DateTime) {
                    $now = new \DateTime();
                    $diff = $now->diff($datePaid);
                    $minutes =
                        ($diff->format('%a') * 1440) + // total days converted to minutes
                        ($diff->format('%h') * 60) +   // hours converted to minutes
                        $diff->format('%i');          // minutes
                    if ($minutes <= 30) { // if order with in half an hour
                        $url = $faizPayPaymentGateway->get_return_url($order);
                        if (wp_redirect($url)) {
                            exit;
                        }
                    }
                }
            }
        } else {
            if ($order->needs_payment()) {
                $url = $order->get_cancel_order_url_raw();
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
        return isset($_GET['order']) ? sanitize_text_field($_GET['order']) : "0";
    }
}