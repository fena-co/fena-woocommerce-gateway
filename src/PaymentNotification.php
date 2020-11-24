<?php


namespace FenaCommerceGateway;


use Fena\PaymentSDK\Connection;
use Fena\PaymentSDK\Error;

class PaymentNotification
{

    public static function process($terminal_id, $terminal_secret)
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['status'])) {
            die();
        }
        if (!isset($data['reference'])) {
            die();
        }

        $orderId = $data['reference'];
        $status = $data['status'];
        $amount = $data['amount'];

        $order = new \WC_Order($orderId);

        if ($order->get_id() == '') {
            die();
        }

        if ($status == 'paid') {
            error_log( "Should succeed" );
            $order->add_order_note("Fena Order ID {$orderId}", 0);
            $order->add_order_note("Fena Net Amount £{$amount}", 0);
            $order->payment_complete();
        }
        if ($status == 'rejected') {
            $order->add_order_note("The payment for id {$orderId} has been cancelled by the customer", 0);
            $order->cancel_order();
        }
        exit();
    }

}
