<div id="personalAccountBalanceTopupTrade" class="personal_account_balance_popup" style="display:none;">
    <div id="smart-button-container">
        <div style="text-align: center; margin-bottom: 5px;"><label for="email"> </label><input name="email"
                type="email" id="email" value="" class="paypal_input" placeholder="<?= 'Enter your email' ?>" oninput="this.classList.remove('is-invalid')">
        </div>
        <div id="email-text-describe" style="text-align: center; margin-bottom: 10px;">
            <p><strong>The wallpaper will be sent to the email address you provided.</strong></p>
        </div>
        <div style="text-align: center;">
            <div id="paypal-button-container"></div>
        </div>
    </div>
</div>
<script
    src="https://www.paypal.com/sdk/js?client-id=<?= PAYPAL_CLIENT_ID ?>&enable-funding=venmo&currency=USD"
    data-sdk-integration-source="button-factory"></script>
<script>
    function initPayPalButton(cost, image_id, prepareCheck) {
        paypal.Buttons({
            style: {
                shape: 'rect',
                color: 'gold',
                layout: 'vertical',
                label: 'paypal',

            },

            createOrder: function (data, actions) {
                var email = document.querySelector('#email').value;
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email)) {
                    document.querySelector('#email').classList.add('is-invalid');
                    return;
                } else {
                    document.querySelector('#email').readOnly = true;
                }
                return actions.order.create({
                    purchase_units: [{ "description": `email:${email};image:${image_id}`, "amount": { "currency_code": "USD", "value": Number(cost) } }]
                });
            },

            onApprove: function (data, actions) {
                return actions.order.capture().then(function (orderData) {
                    var email = document.querySelector('#email').value;
                    let form_data = new FormData();
                    form_data.append('email', email);
                    form_data.append('image_id', image_id);
                    form_data.append('type', 'buy_image');
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
                    document.querySelector('#email-text-describe').style.display = 'none';
                    // document.querySelector('#wayforpay-method-cover').style.display = 'none';
                    const element = document.getElementById('paypal-button-container');
                    element.innerHTML = '';
                    element.innerHTML = '<h3 class="paypal-success">Links to download the images will be sent to your email' + '</h3>';

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