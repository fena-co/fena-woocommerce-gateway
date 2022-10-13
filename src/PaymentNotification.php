<?php


namespace FenaCommerceGateway;


use Fena\PaymentSDK\Connection;
use Fena\PaymentSDK\Error;
use Fena\PaymentSDK\Payment;

class PaymentNotification
{

    public static function process($terminal_id, $terminal_secret)
    {
        global $woocommerce;

        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['status'])) {
            die();
        }
        if (!isset($data['reference'])) {
            die();
        }

        $order_number = $data['reference'];
        $status = $data['status'];
        $amount = $data['amount'];

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

        $hashedId = $order->get_meta('_fena_payment_hashed_id');

        $connection = Connection::createConnection(
            $terminal_id,
            $terminal_secret
        );

        if ($connection instanceof Error) {
            return array(
                'result' => 'failure',
                'messages' => 'Something went wrong. Please contact support.'
            );
        }

        $payment = Payment::createPayment(
            $connection,
            $order->get_total(),
            $orderId
        );

        if ($order->get_id() == '') {
            die();
        }

        $serverData = $payment->checkStatusByHashedId($hashedId);


        if ($serverData['data']['status'] != $status) {
            $status = $serverData['data']['status'];
        }

        if ($serverData['data']['transaction']) {
            $transaction_id = $serverData['data']['transaction'];
            $order->set_transaction_id($transaction_id);
            $order->add_order_note("Fena Transaction ID {$transaction_id}", 0);
        }

        if ($status == 'paid') {
            $order->add_order_note("WooCommerce Default Order ID {$orderId}", 0);
            $order->add_order_note("WooCommerce Order Number (Fena Reference): {$order_number}", 0);
            $order->add_order_note("Fena Net Amount £{$amount}", 0);
            $order->payment_complete();
            $woocommerce->cart->empty_cart();
        }
        if ($status == 'rejected') {
            $order->add_order_note("WooCommerce Default Order ID {$orderId}", 0);
            $order->add_order_note("WooCommerce Order Number (Fena Reference): {$order_number}", 0);
            $order->add_order_note("The payment has been cancelled by the customer", 0);
            $order->cancel_order();
        }
        exit();
    }

}
