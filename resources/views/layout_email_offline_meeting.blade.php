<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
</head>

<body style="max-width: 600px; font-size:16px;">
    <table>
        <tr>

            <td colspan="3"><img src="{{config('services.imgsEmails.imgs')}}header.png" alt="" /></td>
        </tr>
        <tr>
            <td style="width: 180px;"></td>
            <td><img src="{{config('services.imgsEmails.imgs')}}hola.png" alt="banner_correo" style="
                text-align: center;
                margin-bottom: 20px;
                margin-top: 10px;" /></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td>
                <div style="border:1px solid #6fb258;padding: 70px;width: 458px;">
                    <p style="text-align: center">¡Gracias por confiar en
                        <img src="{{config('services.imgsEmails.imgs')}}logo-ico.png" alt="ATA" style="width:30px; vertical-align:middle;"></span>
                    ! </p>
                    <p style="text-align: justify">Para continuar con tu asesoría en línea te pedimos realizar el pago de este
                        <a href={{$url}}>recibo</a> en las 24 hrs. </p>
                    <p style="text-align:justify">De lo contrario tu fecha y día agendada se perderán.</p>
                </div>
            </td>
            <td></td>

        </tr>

        @include('footeremail')

    </table>
    </div>
</body>

</html>