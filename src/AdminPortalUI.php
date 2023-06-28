<?php


namespace FenaCommerceGateway;


class AdminPortalUI
{
    public static function get($settings)
    {
        $saved_dropdown_value = get_option('fena_bank_dropdown', 'Default');
        ?>
        <h2>Fena Payment Gateway</h2>
        <table class="form-table">
            <?php echo $settings; ?>
            <tr valign="top">
                <th scope="row">Bank Selection</th>
                <td>
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <select name="fena_bank_dropdown">
                            <option value="Default" <?php selected($saved_dropdown_value, 'Default'); ?>>Default</option>
                            <!-- Add more options here if needed -->
                        </select>
                        <button type="button" class="button button-primary" id="loadBanks">Load Banks</button>
                    </div>
                </td>
            </tr>
        </table>

        <h4>Payment Notification URL</h4>
        <pre><?php echo home_url('/wc-api/fena', 'https'); ?></pre>

        <h4>Redirect URL</h4>
        <pre><?php echo wc_get_endpoint_url( 'order-received', '', wc_get_checkout_url() ); ?></pre>
        <script>
    document.addEventListener('DOMContentLoaded', function () {
        var loadBanksButton = document.getElementById('loadBanks');
        loadBanksButton.addEventListener('click', function () {
            // Add options to the dropdown
            var dropdown = document.querySelector('select[name="fena_bank_dropdown"]');
            
            // Clear existing options
            dropdown.innerHTML = '';

            // Add new options
            var option1 = document.createElement('option');
            option1.value = 'Default';
            option1.textContent = 'Default';
            dropdown.appendChild(option1);

            var option2 = document.createElement('option');
            option2.value = 'Bank1';
            option2.textContent = 'Bank 1';
            dropdown.appendChild(option2);

            var option3 = document.createElement('option');
            option3.value = 'Bank2';
            option3.textContent = 'Bank 2';
            dropdown.appendChild(option3);
        });
    });
</script>
        <?php
    }
}
