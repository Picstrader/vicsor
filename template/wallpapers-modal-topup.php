<div id="wallpapersTopup" class="personal_account_balance_popup" style="display:none;">
    <div id="smart-button-container">
        <div style="text-align: center;">
            <div id="paypal-button-container"></div>
        </div>
    </div>
</div>
<script
    src="https://www.paypal.com/sdk/js?client-id=<?= PAYPAL_CLIENT_ID ?>&enable-funding=venmo&currency=USD"
    data-sdk-integration-source="button-factory"></script>
<script>
    function initPayPalButton(cost, set_id, prepareCheck) {
        paypal.Buttons({
            style: {
                shape: 'rect',
                color: 'gold',
                layout: 'vertical',
                label: 'paypal',

            },

            createOrder: function (data, actions) {
                return actions.order.create({
                    purchase_units: [{ "description": `set:${set_id}`, "amount": { "currency_code": "USD", "value": Number(cost) } }]
                });
            },

            onApprove: function (data, actions) {
                return actions.order.capture().then(function (orderData) {
                    let form_data = new FormData();
                    form_data.append('type', 'buy_slot');
                    form_data.append('set_id', set_id);
                    form_data.append('action', 'paypal');
                    form_data.append('order', JSON.stringify(orderData));
                    let xhr = new XMLHttpRequest();
                    xhr.open("POST", location.origin + "/index-ajax.php", true);
                    xhr.onreadystatechange = function () {
                        if (xhr.readyState === 4 && xhr.status === 200) {
                            let json = xhr.responseText;
                            prepareCheck(orderData.purchase_units[0].payments.captures[0].id);
                        }
                    };
                    xhr.send(form_data);
                    // Show a success message within this page, e.g.
                    const element = document.getElementById('paypal-button-container');
                    element.innerHTML = '';
                    element.innerHTML = '<h3 class="paypal-success">You will be added to set soon' + '</h3>';

                    // Or go to another URL:  actions.redirect('thank_you.html');

                });
            },

            onError: function (err) {
                console.log(err);
            }
        }).render('#paypal-button-container');
    }
    //initPayPalButton();
</script>