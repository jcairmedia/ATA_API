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
                    <p style="text-align: center"></p>
                    <p style="text-align: justify">
                        Te notificamos que tu asesoría legal programada
                        para el día {{$day}} de {{$month}} ha sido reprogramada
                        para el día {{$dayRe}} de {{$monthRe}} a las {{$hours}}hrs.
                    </p>
                    @if($type_meeting == "VIDEOCALL")
                            @if($zoomObj['code'] == 200)
                            <p style="text-align: center;">
                                <a
                                href="{{$zoomObj['data']['join_url']}}"
                                style="display:inline-block;
                                    background: #6fb258;
                                    padding: 15px;
                                    color: white;
                                    text-decoration: none;">
                                    Enlace para su cita </a>
                            </p>
                            @else
                                <p>{{$zoomObj['message']}}</p>
                            @endif
                    @endif
                </div>
            </td>
            <td></td>
        </tr>
        @include('footeremail')

    </table>
    </div>
</body>

</html>