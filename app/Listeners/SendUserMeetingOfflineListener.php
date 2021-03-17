<?php

namespace App\Listeners;

use App\Main\Contact\Domain\FindContactDomain;
use App\Main\CP\Domain\FindCPByIdTableDomain;
use App\Main\FederalEntities\Domain\FindFederalEntitiesDomain;
use App\Main\Meetings\Domain\FindMeetingDomain;
use App\Main\Meetings\Services\SendNewUserService;

class SendUserMeetingOfflineListener
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
    public function handle($event)
    {
        $idMeeting = $event->idMeeting;
        \Log::error('Hook - charge: '.$idMeeting);

        $meetingObj = (new FindMeetingDomain())(['id' => $idMeeting]);
        \Log::error('Hook - Meeting : '.print_r($meetingObj, 1));

        if (is_null($meetingObj)) {
            return;
        }
        if (!is_null($meetingObj->contacts_id)) {
            $contactObj = (new FindContactDomain())(['id' => $meetingObj->contacts_id]);

            \Log::error('Hook - Contact : '.print_r($contactObj, 1));

            $address = $this->getAddress($contactObj->toArray());
            $feObj = (new FindFederalEntitiesDomain())(['id' => $meetingObj->idfe]);

            \Log::error('Hook - Federal : '.print_r($feObj->toArray(), 1));

            $customer = [
                'NOMBRES' => $contactObj->name,
                'APELLIDO_PATERNO' => $contactObj->lastname_1,
                'APELLIDO_MATERNO' => $contactObj->lastname_2,
                'CURP' => $contactObj->curp,
                'ENTIDAD_FEDERATIVA' => $feObj->idfe,
                'DOMICILIO' => $address,
                'CORREO' => $contactObj->email,
                'TELEFONO_FIJO' => $contactObj->phone,
                'TELEFONO_MOVIL' => $contactObj->phone,
            ];
            \Log::error('Hook - Customer : '.print_r($customer, 1));

            try {
                (new SendNewUserService())($customer);
            } catch (\Exception $th) {
            }
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
}
