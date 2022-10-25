<?php


namespace FenaCommerceGateway;


use Fena\PaymentSDK\Connection;
use Fena\PaymentSDK\DeliveryAddress;
use Fena\PaymentSDK\Error;
use Fena\PaymentSDK\Helper\NumberFormatter;
use Fena\PaymentSDK\Item;
use Fena\PaymentSDK\Payment;
use Fena\PaymentSDK\User;

class PaymentProcess
{

    public static function process($order_id, $terminal_id, $terminal_secret)
    {

//        ini_set('display_errors', 1);
//        ini_set('display_startup_errors', 1);
//        error_reporting(E_ALL);

        $order = new \WC_Order($order_id);

        $previousUrl = $order->get_meta('_fena_payment_url');

        if (isset($previousUrl)) {
            return array(
                'result' => 'success',
                'redirect' => $previousUrl
            );
        }

        $order_number = $order->get_order_number(); // To be used with the Custom Order Numbers For Woocommerce Plugin
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

        if ($order->get_total() < 0.50) {
            return array(
                'result' => 'failure',
                'messages' => 'Total amount should be greater than 0.50.'
            );
        }

        $payment = Payment::createPayment(
            $connection,
            $order->get_total(),
            $order_number
        );

        if ($payment instanceof Error) {
            return array(
                'result' => 'failure',
                'messages' => 'Something went wrong. Please contact support.'
            );
        }

        $user = User::createUser(
            $order->get_billing_email(),
            $order->get_billing_first_name(),
            $order->get_billing_last_name(),
            $order->get_billing_phone()
        );

        if ($user instanceof Error) {
            return array(
                'result' => 'failure',
                'messages' => 'Something went wrong. Please contact support.'
            );
        }

        // payment object
        $payment->setUser($user);

        // add items in the cart
        foreach ($order->get_items() as $item) {
            $item = Item::createItem(
                $item->get_name(),
                $item->get_quantity()
            );
            if ($item instanceof Item) {
                $payment->addItem($item);
            }
        }

        // add delivery address
        if ($order->get_shipping_address_1() != '') {
            $country = $order->get_billing_country();

            if ($country == 'GB') {
                $country = 'UK';
            }

            if (strlen($country) != '2') {
                $country = 'UK';
            }

            $deliveryAddress = DeliveryAddress::createDeliveryAddress(
                $order->get_shipping_address_1(),
                $order->get_shipping_address_2(),
                $order->get_shipping_postcode(),
                $order->get_shipping_city(),
                $country
            );
        } else {
            $country = $order->get_billing_country();

            if ($country == 'GB') {
                $country = 'UK';
            }

            if (strlen($country) != '2') {
                $country = 'UK';
            }

            $deliveryAddress = DeliveryAddress::createDeliveryAddress(
                $order->get_billing_address_1(),
                $order->get_billing_address_2(),
                $order->get_billing_postcode(),
                $order->get_billing_city(),
                $country
            );
        }

        if ($deliveryAddress instanceof DeliveryAddress) {
            $payment->setDeliveryAddress($deliveryAddress);
        }

        $url = $payment->process();

        $order->update_status('awaiting_payment', 'Awaiting payment');

        $hashed = $payment->getHashedId();

        $order->add_meta_data( '_fena_payment_hashed_id', $hashed );
        $order->add_meta_data( '_fena_payment_url', $url );
        $order->save_meta_data();

        return array(
            'result' => 'success',
            'redirect' => $url
        );
    }

}
