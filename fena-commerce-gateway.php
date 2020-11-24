<?php
/*
Plugin Name:       Fena ECommerce
Plugin URI:        https://github.com/fena-co/fena-woocommerce-gateway
Description:       Enables the Fena payment option on woocommerce.
Version:           1.0.0
Author:            Fena
Author URI:        https://www.fena.co
Text Domain:       fena

Fena ECommerce Plugin is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
any later version.

Fena ECommerce Plugin is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Fena ECommerce Plugin. If not, see https://www.gnu.org/licenses/gpl-3.0.html.
*/
//  auto load
if (!file_exists(plugin_dir_path(__FILE__) . 'vendor/autoload.php')) {
    die;
}
require_once plugin_dir_path(__FILE__) . 'vendor/autoload.php';


function fena_woocommerce_stripe_missing_wc_notice()
{
    echo '<div class="error"><p><strong>Fena requires WooCommerce to be installed and active. You can download <a href="https://woocommerce.com/" target="_blank">WooCommerce</a> from here.</strong></p></div>';
}


function woocommerce_gateway_fena_init()
{
    if (!class_exists('WooCommerce')) {
        add_action('admin_notices', 'fena_woocommerce_stripe_missing_wc_notice');
        return;
    }

    add_filter('woocommerce_payment_gateways', 'addFenaPaymentGateway');
    function addFenaPaymentGateway($gateways)
    {
        $gateways[] = 'FenaCommerceGateway\\FenaPaymentGateway';
        return $gateways;
    }

    require plugin_dir_path(__FILE__) . "src/FenaPaymentGateway.php";
}


add_action('plugins_loaded', 'woocommerce_gateway_fena_init');
