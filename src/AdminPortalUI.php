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
                    <label for="dropdown">Select Bank:</label>
                </th>
                <td>
                    <select name="dropdown" id="dropdown" class="input-text regular-input">
                        <option value="option1">Default</option>
                        <option value="option2">Bank 1</option>
                        <option value="option3">Bank 2</option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"></th>
                <td>
                    <button type="button" id="saveBankButton" class="button-primary">Save Bank</button>
                </td>
                
            </tr>
            
        </table>
        <h4>Payment Notification URL</h4>
        <pre><?php echo home_url('/wc-api/fena', 'https'); ?></pre>

        <h4>Redirect URL</h4>
        <pre><?php echo wc_get_endpoint_url('order-received', '', wc_get_checkout_url()); ?></pre>
        
       
       
       
       <script>
    document.addEventListener('DOMContentLoaded', function() {
        var dropdown = document.getElementById('dropdown');
        var terminalSecretField = document.getElementById('woocommerce_fena_payment_terminal_secret');
        var terminalIdField = document.getElementById('woocommerce_fena_payment_terminal_id');
        var saveBankButton = document.getElementById('saveBankButton');

        var isDropdownVisible = (terminalSecretField.value !== '' && terminalIdField.value !== '');
        dropdown.style.display = isDropdownVisible ? 'block' : 'none';
        saveBankButton.style.display = isDropdownVisible ? 'block' : 'none';

        if (isDropdownVisible) {
            fetchDataAndPopulateDropdown();
        }

        function fetchDataAndPopulateDropdown() {
            // Check if terminal ID and secret are not empty or null
            if (terminalIdField.value && terminalSecretField.value) {
                // Make an API call to fetch the data
                // Replace 'apiEndpoint' with the actual API endpoint URL
                fetch('https://api.agify.io/?name=michael')
                    .then(response => response.json())
                    .then(data => {


                        console.log(data);
                        // // Populate the dropdown with the fetched data
                        // dropdown.innerHTML = ""; // Clear existing options

                        // data.forEach(option => {
                        //     const newOption = document.createElement("option");
                        //     newOption.value = option.value;
                        //     newOption.text = option.text;
                        //     dropdown.appendChild(newOption);
                        // });
                    })
                    .catch(error => console.error("Error fetching data:", error));
            }
        }
    });
</script>



<script>
    document.getElementById("saveBankButton").addEventListener("click", saveBankValue);

    function saveBankValue() {
        const dropdown = document.getElementById("dropdown");
        const selectedValue = dropdown.value.toString();

        console.log(selectedValue);

        // Save the selected value to the database using WooCommerce's update_option() function
        updateSelectedValue(selectedValue);

        console.log('Selected value saved to the database:', selectedValue);
    }

    function updateSelectedValue(selectedValue) {

        console.log("this one is from the desired function : ", selectedValue);
        // Use WordPress core function update_option() to save the selected value to the database
        <?php
        // update_option('selected_bank_value',);
        ?>
    }
</script>
        <?php
    }
}
