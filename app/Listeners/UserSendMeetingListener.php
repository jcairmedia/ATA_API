<?php

namespace App\Listeners;

use App\Events\UserSendMeetingEvent;
use App\Main\CP\Domain\FindCPByIdTableDomain;
use App\Main\FederalEntities\Domain\FindFederalEntitiesDomain;
use App\Main\Meetings\Services\SendNewUserService;
use GuzzleHttp\Client;

class UserSendMeetingListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @param object $event
     *
     * @return void
     */
    public function handle(UserSendMeetingEvent $event)
    {
        /*
         *  "NOMBRES": "",
            "APELLIDO_PATERNO": "",
            "APELLIDO_MATERNO": "",
            "CURP": "",
            "ENTIDAD_FEDERATIVA": "",
            "DOMICILIO":"",
            "CORREO": "",
            "TELEFONO_FIJO": "",
            "TELEFONO_MOVIL": ""
        */

        // Buscar entidad federativa
        // Buscar domicilio

        $fe = (new FindFederalEntitiesDomain())(['id' => $event->meeting['idfe']]);

        $address = $this->getAddress($event->contact);

        $customer = [
            'NOMBRES' => $event->contact['name'],
            'APELLIDO_PATERNO' => $event->contact['lastname_1'],
            'APELLIDO_MATERNO' => $event->contact['lastname_2'],
            'CURP' => $event->contact['curp'],
            'ENTIDAD_FEDERATIVA' => $fe['idfe'],
            'DOMICILIO' => $address,
            'CORREO' => $event->contact['email'],
            'TELEFONO_FIJO' => $event->contact['phone'],
            'TELEFONO_MOVIL' => $event->contact['phone'],
        ];

        \Log::error('UserSendMeetingListener: '.print_r($event->contact, 1));
        try {
            (new SendNewUserService())($customer);
        } catch (\Exception $th) {
        }
    }

    private function getAddress($contact)
    {
        $cp = (new FindCPByIdTableDomain())(['id' => $contact['idcp']]);
        $address = $contact['street'].', '.$contact['out_number'].', ';

        $address .= array_key_exists('int_number', $contact) ? $contact['int_number'].', ' : ' , ';

        $address .= $cp->D_mnpio.', '.
                      $cp->d_asenta.', '.
                      $cp->d_asenta;

        return $address;
    }

    private function send($data)
    {
        $client = new Client(['http_errors' => false]);
        try {
            $response = $client->request(
                'POST',
                env('URL_SOFTWARE_ATA'), [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'body' => json_encode($data),
                ]);
            $statusCode = (int) $response->getStatusCode();

            $json_response = (string) $response->getBody();
            if ($statusCode != 200) {
                throw new \Exception('URL_SOFTWARE_ATA: '.$response->getReasonPhrase().(json_decode($json_response, true)['description']), $statusCode);
            }

            return $json_response;
        } catch (\RequestException  $e) {
            \Log::error('SERVICE: '.$e->getMessage(), $e->getCode());
            throw new \Exception($e->getMessage());
        }
    }
}
