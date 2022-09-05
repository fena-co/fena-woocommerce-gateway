<?php


namespace FenaCommerceGateway;


use Fena\PaymentSDK\Connection;
use Fena\PaymentSDK\Error;
use Fena\PaymentSDK\Payment;

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

        error_log( print_r($serverData, TRUE) );

        if ($serverData['data']['status'] != $status) {
            $status = $serverData['data']['status'];
        }

        if ($status == 'paid') {
            error_log( "Should succeed" );
            $order->add_order_note("Fena Order ID {$orderId}", 0);
            $order->add_order_note("Fena Net Amount £{$amount}", 0);
            $order->payment_complete();
        }
        if ($status == 'rejected') {
            error_log( "Should reject" );
            $order->add_order_note("The payment for id {$orderId} has been cancelled by the customer", 0);
            $order->cancel_order();
        }
        exit();
    }

}
