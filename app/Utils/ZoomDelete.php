<?php

namespace App\Utils;

class ZoomDelete
{
    public function __construct($userId, $token)
    {
        $this->userId = $userId;
        $this->token = $token;
    }

    public function __invoke($meetingZoomId)
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
                    CURLOPT_URL => 'https://api.zoom.us/v2/meetings/'.$meetingZoomId,
                    CURLOPT_SSL_VERIFYPEER => 1,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_CUSTOMREQUEST => 'DELETE',
                    CURLOPT_HTTPHEADER => [
                        'Authorization: Bearer '.$this->token,
                        'content-Type: application/json',
                    ],
                ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        \Log::error('eliminacion de zoom: '.print_r($response, 1));
        if ($err) {
            \Log::error('Er: '.print_r($response, 1));
        }
        curl_close($curl);
        \Log::error('eliminacion22 de zoom: '.print_r($response, 1));

        $json = json_decode($response, true);
        if (isset($json['code'])) {
            \Log::error('eliminacion de zoom: '.print_r($response, 1));
        }

        return $json;
    }
}
