<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
</head>

<body style="max-width: 600px">
    <table>
        <tr>

            <td colspan="3"><img src="{{config('services.imgsEmails.imgs')}}header.png" alt="" /></td>
        </tr>
        <tr>
            <td style="width: 180px;"></td>
            <td><img src="{{config('services.imgsEmails.imgs')}}banner_correo.jpg" alt="banner_correo" style="
                text-align: center;
                margin-bottom: 20px;
                margin-top: 10px;" /></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td>
                <div style="border:1px solid #6fb258;padding: 70px;width: 458px;">
                    <p style="text-align: center">Gracias por confiar en ATA </p>
                    <p style="text-align: justify">
                        Te notificamos que hemos recibido confirmación de tu pago para nuestro servicio
                        de asesoría legal en nuestro <strong>"Paquete {{$package}}"</strong>.

                    </p>
                    <p>
                        Si tienes dudas, por favor escríbenos o comunícate a nuestros números de atención al cliente.
                    </p>
                </div>
            </td>
            <td></td>

        </tr>
        <tr>
            <td></td>
            <td><img src="{{config('services.imgsEmails.imgs')}}footer.png" alt=""></td>
            <td></td>

        </tr>
        @include('footeremail')

    </table>
    </div>
</body>

</html>