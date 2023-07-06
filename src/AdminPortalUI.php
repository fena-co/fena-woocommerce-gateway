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
            <tr>
                <th scope="row">
                    <label for="loadbanks">Load Banks:</label>
                </th>
                <td>
                    <button id="loadbanks" class="button button-primary">Load Banks</button>
                </td>
            </tr>
        </table>

        <h4>Payment Notification URL</h4>
        <pre><?php echo home_url('/wc-api/fena', 'https'); ?></pre>

        <h4>Redirect URL</h4>
        <pre><?php echo wc_get_endpoint_url('order-received', '', wc_get_checkout_url()); ?></pre>
        <script>
    document.addEventListener('DOMContentLoaded', function() {
        var dropdown = document.getElementById('woocommerce_fena_payment_dropdown');
        var terminalSecretField = document.getElementById('woocommerce_fena_payment_terminal_secret');
        var terminalIdField = document.getElementById('woocommerce_fena_payment_terminal_id');
        var LoadButton = document.getElementById('loadbanks');
        

        var isDropdownVisible = (terminalSecretField.value !== '' && terminalIdField.value !== '');
        dropdown.style.display = isDropdownVisible ? 'block' : 'none';
        LoadButton.style.display = isDropdownVisible ? 'block' : 'none'; 

        if(isDropdownVisible){
            if (terminalIdField.value && terminalSecretField.value) {
                // Make an API call to fetch the data
                // Replace 'apiEndpoint' with the actual API endpoint URL
 
        }
        }



 
    });

    document.getElementById("loadbanks").addEventListener("click", LoadButton);
    var dropdown = document.getElementById('woocommerce_fena_payment_dropdown');
    function LoadButton(e) {
        e.preventDefault();
        fetch('https://api.agify.io/?name=michael')
                    .then(response => response.json())
                    .then(data => {
                         data1 = ["Default", "Bank 1", "Bank 2"];
                        // Populate the dropdown with the fetched data
                        dropdown.innerHTML = ""; // Clear existing options
                        data1.forEach(option => {
                           const newOption = document.createElement("option");
                            newOption.value = option;
                            newOption.text = option;
                           dropdown.appendChild(newOption);
});
                        console.log(data);
               })
                    .catch(error => console.error("Error fetching data:", error));
        
}
   
</script>

        <?php
    }
}