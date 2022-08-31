<?php


namespace FenaCommerceGateway;


class CheckoutIcon
{
    public static function get($id)
    {
        $icon_html = '';
        $providers = array('bank-payment');
        foreach ($providers as $provider) {
            $url = \WC_HTTPS::force_https_url(plugin_dir_url(dirname(__FILE__, 1)) . 'assets/' . $provider . '.svg');
            $icon_html .= '<img style="vertical-align: bottom;" width="26" src="' . esc_attr($url) . '" alt="' . esc_attr($provider) . '" />';
        }
        $icon_html .= '
        <style>
@media only screen and (max-width: 1000px) {
  .payment_method_fena_payment label img {
    vertical-align: bottom;
  }
}
</style>
';
        return apply_filters('woocommerce_gateway_icon', $icon_html, $id);
    }
}
