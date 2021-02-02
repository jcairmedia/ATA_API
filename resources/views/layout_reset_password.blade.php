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
            <td style="width: 420px">
                <p style="text-align: center; font-size:1.5em; font-weight: bold">Recuperación de contraseña</p>
            </td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td>
                <div style="border:1px solid #6fb258;padding: 70px;width: 458px;">
                    <p style="text-align: center">
                        Su nueva contraseña es <strong>{{$password}}</strong>
                    </p>
                    <p></p>
                </div>
            </td>
            <td></td>
        </tr>
    </table>
    </div>
</body>

</html>