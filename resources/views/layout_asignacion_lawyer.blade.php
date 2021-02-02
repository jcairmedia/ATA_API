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
            <td><img src="{{config('services.imgsEmails.imgs')}}banner_correo.png" alt="banner_correo" style="
                text-align: center;
                margin-bottom: 20px;
                margin-top: 10px;" /></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td>
                <div style="border:1px solid #6fb258;padding: 70px;width: 458px;">
                    <p style="text-align: justify">¡Hola!  En ATA, estamos contentos de ser tu elección para protección y acompañamiento legal.</p>
                    <p style="text-align: justify">Tu caso lo llevará el/la Lic. <strong>{{$lawyer}}</strong>, al cuál podrás encontrar en el siguiente número y correo:</p>
                    <p style="text-align: center;">Teléfono: <strong style="color:#6fb258;">{{$phone}}</strong></p>
                    <p style="text-align: center;">Correo electrónico: <strong style="color:#6fb258;">{{$email}}</strong></p>
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