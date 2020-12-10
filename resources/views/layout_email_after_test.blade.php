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
            <td style="width: 180px;">&nbsp;</td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td>
                <div style="border:1px solid #6fb258;padding: 70px;width: 458px; text-align: center">
                    <h3>Gracias por confiar en nostros</h3>
                    @if( $category == "FREE")
                        <p>Hola</p>
                        <p>Esperamos que la guía con nuestros asesores te haya sido de ayuda.</p>
                        <p>Si deseas continuar con una asesoría formal con uno de
                            nuestros abogados, te invitamos a seguir el enlace que
                            se encuentra debajo.
                        </p>
                        <p>
                            La asesoría tiene un costo de {{$price}} y puedes agendarla
                            de forma sencilla, ahora mismo.
                        </p>
                        <a href="{{$link}}">Agenda tu asesoría</a>
                    @else
                        <p>
                            En ATA estamos muy contentos de poder ofrecerte
                            la mejor asesoría legal, asi como te lo comunico
                            tu abogado, si quieres continuar con tu proceso legal,
                            te invitamos a realizar el pago de nuestros servicio y la firma de tu contrato.
                        </p>
                        <a href="{{$link}}">Contrata aquí</a>
                        <p>Cualquier duda o aclaración, contesta este correo o comunícate
                            a los números de atención al cliente.
                        </p>
                    @endif
                </div>
            </td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td>
                <div style="margin-top:50px;width: 580px;">
                    <p style="text-align:center; padding:0;margin:0;color:#6fb258">ATENCIÓN AL CLIENTE:</p>
                    <p style="text-align:center; padding:0;margin:0;">
                        WhatsApp: 55-7974-9028
                    </p>
                    <p style="text-align:center; padding:0;margin:0;">
                        Vía telefónica: 55-2625-0649
                    </p>
                    <p style="text-align:center;">
                        <a style="color:#6fb258">Aviso de Privacidad</a>
                    </p>
                </div>
            </td>
            <td></td>

        </tr>
    </table>
    </div>
</body>

</html>