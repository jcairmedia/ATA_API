<body>
    <form id="customer-form">
        <input type="hidden" name="token_id" id="token_id" />
        <fieldset>
            <legend>Datos del cliente</legend>
            <p>
                <label>Nombre</label>
                <input type="text" size="20" autocomplete="on" name="client_name" />
            </p>
            <p>
                <label>Correo Electr&oacute;nico</label>
                <input type="text" size="20" autocomplete="on" name="cliente_email" />
            </p>
        </fieldset>
        <fieldset>
            <legend>Datos de la tarjeta</legend>
            <p>
                <label>Nombre</label>
                <input type="text" size="20" autocomplete="off" data-openpay-card="holder_name" />
            </p>
            <p>
                <label>N&uacute;mero</label>
                <input type="text" size="20" autocomplete="off" data-openpay-card="card_number" />
            </p>
            <p>
                <label>CVV2</label>
                <input type="text" size="4" autocomplete="off" data-openpay-card="cvv2" />
            </p>
            <p>
                <label>Fecha de expiraci&oacute;n (MM/YY)</label>
                <input type="text" size="2" data-openpay-card="expiration_month" /> /
                <input type="text" size="2" data-openpay-card="expiration_year" />
            </p>
        </fieldset>
        <input type="submit" id="save-button" value="Pagar" />
    </form>
</body>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script type="text/javascript" src="https://js.openpay.mx/openpay.v1.min.js"></script>
<script type='text/javascript' src="https://js.openpay.mx/openpay-data.v1.min.js"></script>

<script>
    $(document).ready(function() {
        OpenPay.setId('me4rw2430fbizvozxcq1');
        OpenPay.setApiKey('pk_8a6c8a94059942f393931419f3ed79ad');
        OpenPay.setSandboxMode(true);
        var deviceSessionId = OpenPay.deviceData.setup("customer-form", "deviceIdHiddenFieldName");


        $('#save-button').on('click', function(event) {
            event.preventDefault();
            $("#save-button").prop("disabled", true);
            OpenPay.token.extractFormAndCreate('customer-form', success_callbak, error_callbak);
        });
        var success_callbak = function(response) {
            var token_id = response.data.id;
            console.dir(response);
            $('#token_id').val(token_id);
            console.dir($("#customer-form").serializeArray());
        };
        var error_callbak = function(response) {
            var desc = response.data.description != undefined ? response.data.description : response.message;
            alert("ERROR [" + response.status + "] " + desc);
            $("#save-button").prop("disabled", false);
        };

    });
</script>