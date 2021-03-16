<?php

namespace App\Main\Meetings\UseCases;

use App\Main\Meetings\Domain\MeetingCreatorDomain;
use App\Meeting;

class MeetingRegisterUseCase
{
    public function __construct()
    {
    }

    public function __invoke(
        array $data,
        int $contact_id,
        string $durationMeeting,
        int $customer_id)
    {
        //2. Prepare Meeting
        $meetingD = new MeetingCreatorDomain();
        // $amount_meeting = $data['category'] == 'FREE' ? 0 : '';
        // $paid = $data['category'] == 'FREE' ? 1 : 0;
        $array = [
            'folio' => $this->generateFolio(),
            'category' => $data['category'],

            'idfe' => $data['idfe'],

            'type_meeting' => $data['type_meeting'],
            'url_meeting' => '',
            'dt_start' => $data['date'].' '.$data['time'],
            'dt_end' => $this->getDate($data['date'].' '.$data['time'], $durationMeeting),
            'price' => $data['amount'],
            'record_state' => 1, // 1: open, 0:close
            'paid_state' => $data['paid'], // 1: paid, 0: no paid
        ];
        if ($contact_id != 0) {
            $array['contacts_id'] = $contact_id;
        } else {
            $array['customer_id'] = $customer_id;
        }
        // 3. Register
        return $meetingD(new Meeting($array));
    }

    private function generateFolio()
    {
        $dt = date('dmYHis');

        return uniqid($dt);
    }

    public function getDate($datetimeStart, string $increment)
    {
        $dt = new \DateTime($datetimeStart);

        return (date_add($dt, date_interval_create_from_date_string($increment)))->format('Y-m-d H:i:s');
    }
}
