<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
</head>

<body>

    <div style="padding-left: 20%;padding-right: 20%;">
        <img src="" alt="">
        <h1 style="text-align: center">En ATA estamos para escucharte y ayudarte en cualquier problema legal que tengas
        </h1>
        <div style="border: 1px solid #6fb258;padding: 70px;">
            <p style="text-align: center">
                Hola {{ $customer_name }} gracias por registrarte en Abogados a tu Alcance
            </p>
            <p style="text-align: center">Por favor confirma tu correo electrónico</p>
            <p style="text-align: center">Para ello simplemente debes hacer click en el siguiente enlace</p>
            <a href="{{$url}}/{{ $confirmation_code}}">Clic para confirmar tu email</a>
        </div>
        <p style="text-align: center">
            <a href="" style="color: #6fb258;text-align: center"  href="{{config('services.footerEmailData.urlprivacity')}}">Aviso de privacidad</a>
        </p>
    </div>

</body>

</html>