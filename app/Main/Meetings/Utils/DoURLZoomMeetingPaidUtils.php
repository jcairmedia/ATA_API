<?php

namespace App\Main\Meetings\Utils;

use App\Main\Config_System\Domain\SearchConfigDomain;
use App\Main\Config_System\UseCases\SearchConfigurationUseCase;
use App\Main\Meetings\Domain\MeetingUpdateDomain;
use App\Main\ZoomRequest\Domain\ZoomRequestDomain;
use App\Main\ZoomRequest\UseCases\RegisterZoomUseCase;
use App\Utils\ZoomMeetings;
use App\ZoomRequest;

class DoURLZoomMeetingPaidUtils
{
    public function __invoke($meeting_id, $date, $type_meeting, $subject)
    {
        $zoomresponse = [
            'code' => 500,
            'message' => '',
            'data' => [],
        ];
        if ($type_meeting != 'VIDEOCALL') {
            return $zoomresponse;
        }
        try {
            $search = new SearchConfigurationUseCase(new SearchConfigDomain());
            $config = $search->__invoke('ZOOM_ACCESS_TOKEN');
            $zoomMeeting = new ZoomMeetings(env('ZOOM_USER_ID'), $config->value);
            $response = $zoomMeeting->build($date.':00', $subject);
            // Save request Zoom
            $dt_meeting_zoom = new \DateTime($response['start_time'], new \DateTimeZone($response['timezone']));

            $zoomRequestArray = [
                'join_url' => $response['join_url'],
                'password' => $response['password'],
                'start_time' => $dt_meeting_zoom->format('Y-m-d H:i:s'),
                'timezone' => $response['timezone'], //$response['start_time'],
                'json' => json_encode($response),
            ];
            $this->saveZoomRequest($zoomRequestArray);
            // update url meeting
            $meetingUpdate = new MeetingUpdateDomain();
            $meetingUpdate->__invoke($meeting_id, ['url_meeting' => $response['join_url']]);
        } catch (\Exception $ex) {
            $zoomRequestArray = [
                'join_url' => '',
                'password' => '',
                'start_time' => $date,
                'state_request' => false,
                'json' => json_encode($ex->getMessage()),
            ];
            $this->saveZoomRequest($zoomRequestArray);

            return [
                'code' => 500,
                'message' => 'Error al obtener la url de zoom.('.$ex->getMessage().').'.
                            ' Contacte a su administrador
                            para que le proporcione una url de reunión.',
            ];
        }

        return
            [
                'code' => 200,
                'data' => $response, ];
    }

    private function saveZoomRequest($array)
    {
        $registerzoom = new RegisterZoomUseCase(new ZoomRequestDomain());
        $registerzoom->__invoke(new ZoomRequest($array));
    }
}
