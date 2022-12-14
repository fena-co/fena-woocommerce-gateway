<?php


namespace FenaCommerceGateway;


class AdminPortalUI
{
    public static function get($settings)
    {
        ?>
        <h2>Fena Payment Gateway</h2>
        <table class="form-table">
            <?php echo $settings; ?>
        </table>

        <h4>Payment Notification URL</h4>
        <pre><?php echo home_url('/wc-api/fena', 'https'); ?></pre>

        <h4>Redirect URL</h4>
        <pre><?php echo wc_get_endpoint_url( 'order-received', '', wc_get_checkout_url() ); ?></pre>
        <?php
    }
}
