<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
</head>

<body style="max-width: 600px; font-size:16px; line-height: 30px;">
    <table>
        <tr>

            <td colspan="3"><img src="{{config('services.imgsEmails.imgs')}}header.png" alt="" /></td>
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
                    <h3>Gracias por confiar en nosotros</h3>
                    @if( $category == "FREE")
                        <p>Hola</p>
                        <p>Esperamos que la guía con nuestros asesores te haya sido de ayuda.</p>
                        <p>Si deseas continuar con una asesoría formal con uno de
                            nuestros abogados, te invitamos a seguir el enlace que
                            se encuentra debajo.
                        </p>
                        <p>
                            La asesoría tiene un costo de ${{$price}} y puedes agendarla
                            de forma sencilla, ahora mismo.
                        </p>
                        <a href="{{$link}}" style=" display:inline-block;
                                                    background: #6fb258;
                                                    padding: 15px;
                                                    color: white;
                                                    text-decoration: none;">
                            Agenda tu asesoría
                        </a>
                    @else
                        <p>
                            En
                            <img src="{{config('services.imgsEmails.imgs')}}logo-ico.png" alt="ATA" style="width:30px; vertical-align:middle;"></span>
                          estamos muy contentos de poder ofrecerte
                            la mejor asesoría legal, asi como te lo comunico
                            tu abogado, si quieres continuar con tu proceso legal,
                            te invitamos a realizar el pago de nuestros servicio y la firma de tu contrato.
                        </p>
                        <a href="{{$link}}" style=" display:inline-block;
                                                    background: #6fb258;
                                                    padding: 15px;
                                                    color: white;
                                                    text-decoration: none;">
                            Contrata aquí</a>
                        <p>Cualquier duda o aclaración, contesta este correo o comunícate
                            a los números de atención al cliente.
                        </p>
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