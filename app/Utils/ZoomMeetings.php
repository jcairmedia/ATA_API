<?php

namespace App\Utils;

class ZoomMeetings
{
    public function __construct($userId, $token)
    {
        $this->userId = $userId;
        $this->token = $token;
    }

    public function build($datetime, $subject)
    {
        \Log::error('Zoom: '.$datetime);
        $date_utc = new \DateTime($datetime);
        $date_utc->setTimezone(new \DateTimeZone('UTC'));
        $date = $date_utc->format('Y-m-d\\TH:i:s\\Z');

        $curl = curl_init();
        $data = [
            'start_time' => $date,
            'timezone' => 'UTC',
            'topic' => $subject,
            'settings' => [
              'host_video' => true,
              'participant_video' => true,
            ],
        ];
        $StringData = json_encode($data);

        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.zoom.us/v2/users/'.$this->userId.'/meetings',
            CURLOPT_SSL_VERIFYPEER => 1,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer '.$this->token,
                'content-Type: application/json',
                'Content-length: '.strlen($StringData),
            ],
            CURLOPT_POSTFIELDS => $StringData,
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        \Log::error('Error curl: '.curl_error($curl));
        if ($err) {
            // echo 'cURL Error #:'.$err;
            throw new \Exception(print_r($err, 1));
        }
        curl_close($curl);

        $json = json_decode($response, true);
        if (isset($json['code'])) {
            throw new \Exception($json['message'], $json['code']);
        }

        return $json;
        // return ['url' => $json['join_url'], 'password' => $json['password']];
    }
}
