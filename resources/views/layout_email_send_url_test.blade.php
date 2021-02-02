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
                    <p style="text-align: center">
                        Esperamos que tu asesoría haya sido de gran ayuda.
                        Con fines de mejorar nuestro servicio nos ayudarias mucho
                        respondiendo la siguiente encuesta
                    </p>
                    <p style="text-align: center">
                        <a href="{{$url}}"
                            style="display:inline-block;
                                    background: #6fb258;
                                    padding: 15px;
                                    color: white;
                                    text-decoration: none;"
                        >Link de la encuesta</a>
                    </p>
                </div>
            </td>
            <td></td>

        </tr>
        <tr>
            <td></td>
            <td><img src="{{config('services.imgsEmails.imgs')}}recomendar.png" alt=""></td>
            <td></td>

        </tr>
        @include('footeremail')

    </table>
    </div>
</body>

</html>