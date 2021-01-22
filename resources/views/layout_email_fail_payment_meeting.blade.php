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

            <td colspan="3"><img src="http://apidev.usercenter.mx/images/header.png" alt="" /></td>
        </tr>
        <tr>
            <td style="width: 180px;"></td>
            <td><img src="http://apidev.usercenter.mx/images/hola.png" alt="banner_correo" style="
                text-align: center;
                margin-bottom: 20px;
                margin-top: 10px;" /></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td>
                <div style="border:1px solid #6fb258;padding: 70px;width: 458px;">
                    <p style="text-align: center">
                        Lamentablemente no hemos recibido el
                        pago para la realización de tu asesoría
                        legal en línea programada para el día {{$day}}
                        de {{$month}} a las {{$time}} hrs, por lo cual queda
                        cancelada.
                    </p>
                    <p style="text-align: center">Si deseas volver a agendar en otra fecha sigue el enlace</p>
                    <p style="text-align: center">
                        <a
                            href={{$link}}
                            style="display:inline-block;
                                        background: #6fb258;
                                        padding: 15px;
                                        color: white;
                                        text-decoration: none;"
                            >Agenda tu asesoría
                        </a>
                    </p>
                </div>
            </td>
            <td></td>
        </tr>
        @include('footeremail')

    </table>
    </div>
</body>

</html>