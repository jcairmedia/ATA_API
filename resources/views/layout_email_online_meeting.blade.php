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
                    @switch($type_meeting)
                        @case("VIDEOCALL")
                            <p style="text-align: center">Hemos recibido y confirmado tu pago para tú asesoria en línea</p>
                            <p style="text-align: justify">Recuerda seguir el enlace que se encuentra debajo,
                                el día <strong>{{$day}}</strong> de <strong>{{$month}}</strong> a las <strong>{{$hours}}</strong> horas
                                , para llevar a cabo tu asesoria con duración de 45 minutos
                            </p>
                            <table>
                                <tr>
                                    <td>-</td>
                                    <td>Tu cita no es reembolsable ni reprogramable</td>
                                </tr>
                                <tr>
                                    <td>-</td>
                                    <td>El tiempo máximo de espera por parte de nuestros abogados es de 15 minutos</td>
                                </tr>
                            </table>
                            @if($zoomObj['code'] == 200)
                                <table>
                                    <tr>
                                        <td style="width: 50%"></td>
                                        <td>
                                        <a href="{{$zoomObj['data']['join_url']}}"
                                            style=" display:inline-block;
                                            background: #6fb258;
                                            padding: 15px;
                                            color: white;
                                            text-decoration: none;"
                                        >Enlace para su cita </a>
                                        </td>
                                    </tr>
                                </table>
                            @else
                                <p>{{$zoomObj['message']}}</p>
                            @endif

                        @break
                        @case("CALL")
                            <p style="text-align: justify">Hemos recibido y confirmado tu pago para tu asesoría legal vía telefónica.</p>
                            <p style="text-align: justify">Recuerda mantenerte atento al número telefónico que nos proporcionaste
                                el día <strong>{{$day}}</strong> de <strong>{{$month}}</strong>
                                a las <strong>{{$hours}}</strong> horas, para llevar a cabo tu
                                asesoría con duración de 45 minutos.
                            </p>
                            <p>A tomar en cuenta:</p>
                            <table>
                                <tr>
                                    <td>-</td>
                                    <td>Tu cita no es reembolsable ni reprogramable</td>
                                </tr>
                                <tr>
                                    <td>-</td>
                                    <td>El tiempo máximo de espera por parte de nuestros abogados es de 15 minutos</td>
                                </tr>
                            </table>
                            <p style="text-align: justify">Si tienes dudas, por favor responde a este correo o comunícate al 55-2625-0649</p>
                        @break
                        @case("PRESENTIAL")
                            <p style="text-align: justify">Hemos recibido y confirmado tu pago para tu asesoría legal en modalidad presenciasl</p>
                            <p style="text-align: justify">A tomar en cuenta:</p>
                            <table>
                                <tr>
                                    <td>-</td>
                                    <td>Tu cita no es reembolsable ni reprogramable</td>
                                </tr>
                                <tr>
                                    <td>-</td>
                                    <td>El tiempo máximo de espera por parte de nuestros abogados
                                        es de 15 minutos</td>
                                </tr>
                            </table>
                            <p>Recuerda acudir a Avenida, Av. Cuauhtémoc 145, Cuauhtémoc,
                                06700 CDMX , el día {{$day}} de {{$month}} a las {{$hours}} horas,
                                para llevar a cabo tu asesoría con duración de 45 minutos.
                            </p>
                        @break
                    @endswitch
                </div>
            </td>
            <td></td>

        </tr>

        @include('footeremail')

    </table>
    </div>
</body>

</html>