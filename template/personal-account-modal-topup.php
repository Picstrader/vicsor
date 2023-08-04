<div id="personalAccountBalanceTopup" class="personal_account_balance_popup" style="display:none;">
    <!-- <div class="personal_account_balance_header">
        <?= $fs['Top-up'] ?>
    </div>
    <input class="top-up-input" placeholder="Enter TxID here"> -->
    <div id="smart-button-container">
        <div style="display: none;"><label for="description"></label><input type="text" name="descriptionInput"
                id="description" maxlength="127" value="User id: <?= ($_SESSION['user_data']['id']); ?>"></div>
        <!--      <p id="descriptionError" style="visibility: hidden; color:red; text-align: center;">Please enter a description</p> -->
        <div style="text-align: center"><label for="amount"> </label><input name="amountInput" type="number" id="amount"
                oninput="calculateTopupFee()" value="" class="paypal_input" placeholder="<?= $fs['Please enter amount USD'] ?>"></div>
        <div style="text-align: center" id="topup-fee"></div>
        <p id="priceLabelError" style="visibility: hidden; color:red; text-align: center;">Please enter an amount</p>
        <div id="invoiceidDiv" style="text-align: center; display: none;"><label for="invoiceid"> </label><input
                name="invoiceid" maxlength="127" type="text" id="invoiceid" value=""></div>
        <p id="invoiceidError" style="visibility: hidden; color:red; text-align: center;">Please enter an Invoice ID</p>
    </div>
    <div style="text-align: center; margin-top: 0.625rem;" id="paypal-button-container"></div>
</div>
<script
    src="https://www.paypal.com/sdk/js?client-id=AT-_GvzapGkuEaFS3owF695CDIcQDZqoHQjBKlM1q2Y1KJBlhaPdww05wbfMqBB3O7Pyv7CTWsIhkAHO&enable-funding=venmo&currency=USD"
    data-sdk-integration-source="button-factory"></script>

<script>
    function initPayPalButton() {
        var description = document.querySelector('#smart-button-container #description');
        var amount = document.querySelector('#smart-button-container #amount');
        //var descriptionError = document.querySelector('#smart-button-container #descriptionError');
        var priceError = document.querySelector('#smart-button-container #priceLabelError');
        var invoiceid = document.querySelector('#smart-button-container #invoiceid');
        var invoiceidError = document.querySelector('#smart-button-container #invoiceidError');
        var invoiceidDiv = document.querySelector('#smart-button-container #invoiceidDiv');

        var elArr = [description, amount];

        if (invoiceidDiv.firstChild.innerHTML.length > 1) {
            invoiceidDiv.style.display = "block";
        }

        var purchase_units = [];
        purchase_units[0] = {};
        purchase_units[0].amount = {};

        function validate(event) {
            return event.value.length > 0;
        }

        paypal.Buttons({
            style: {
                color: 'gold',
                shape: 'rect',
                label: 'paypal',
                layout: 'vertical',

            },

            onInit: function (data, actions) {
                actions.disable();

                if (invoiceidDiv.style.display === "block") {
                    elArr.push(invoiceid);
                }

                elArr.forEach(function (item) {
                    item.addEventListener('keyup', function (event) {
                        var result = elArr.every(validate);
                        if (result) {
                            actions.enable();
                        } else {
                            actions.disable();
                        }
                    });
                });
            },

            onClick: function () {
                //        if (description.value.length < 1) {
                //          descriptionError.style.visibility = "visible";
                //        } else {
                //          descriptionError.style.visibility = "hidden";
                //        }

                if (amount.value.length < 1) {
                    priceError.style.visibility = "visible";
                } else {
                    priceError.style.visibility = "hidden";
                }

                if (invoiceid.value.length < 1 && invoiceidDiv.style.display === "block") {
                    invoiceidError.style.visibility = "visible";
                } else {
                    invoiceidError.style.visibility = "hidden";
                }

                purchase_units[0].description = /*description.value*/"User id: " + <?= getLoginUserId() ?>;
                purchase_units[0].amount.value = amount.value;

                if (invoiceid.value !== '') {
                    purchase_units[0].invoice_id = invoiceid.value;
                }
            },

            createOrder: function (data, actions) {
                return actions.order.create({
                    purchase_units: purchase_units,
                });
            },

            onApprove: function (data, actions) {
                return actions.order.capture().then(function (orderData) {
                    let form_data = new FormData();
                    form_data.append('action', 'paypal');
                    form_data.append('order', JSON.stringify(orderData));
                    let xhr = new XMLHttpRequest();
                    xhr.open("POST", location.origin + "/personal-account-ajax.php", true);
                    xhr.onreadystatechange = function () {
                        if (xhr.readyState === 4 && xhr.status === 200) {
                            let json = xhr.responseText;
                        }
                    };
                    xhr.send(form_data);
                    // Show a success message within this page, e.g.
                    const element = document.getElementById('paypal-button-container');
                    element.innerHTML = '';
                    element.innerHTML = '<h3 class="paypal-success">' + fs['Your balance will be updated within a few minutes'] + '</h3>';
                    let input = document.querySelector('#amount');
                    if (input) {
                        input.parentNode.style.display = 'none';
                    }
                    let fee = document.getElementById('topup-fee');
                    if (fee) {
                        fee.style.display = 'none';
                    }
                });
            },

            onError: function (err) {
                console.log(err);
            }
        }).render('#paypal-button-container');
    }
    initPayPalButton();
</script>