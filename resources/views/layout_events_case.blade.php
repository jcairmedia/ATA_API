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

                    <table>
                        <tr>
                            <td style="color: #535456; width:150px">Hora del evento: </td>
                            <td> {{$dia}} de {{$mes}} a las {{$hora}}</td>
                        </tr>
                        <tr>
                            <td style="color: #535456; width:150px">Asunto: </td>
                            <td> {{$asunto}}</td>
                        </tr>
                        <tr>
                            <td style="color: #535456"> Mensaje del evento:</td>
                        </tr>
                        <tr>
                            <td colspan="2">{{$mensaje}}</td>
                        </tr>
                        @if($url != "")
                        <tr>
                            <td style="color: #535456">Información para unirse</td>
                            <td><a href="{{$url}}">Click Aqui</a></td>
                        </tr>
                        @endif
                    </table>


                </div>

            </td>
            <td></td>

        </tr>

        @include('footeremail')

    </table>
    </div>
</body>

</html>