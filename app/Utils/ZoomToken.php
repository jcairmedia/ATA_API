<?php

namespace App\Utils;

class ZoomToken
{
    public function __construct()
    {
        $this->apikey = env('ZOOM_API_KEY');
        $this->apiSecret = env('ZOOM_API_SECRET');
    }

    public function build()
    {
        $date = new \DateTime();
        $date->modify('+1 day');
        $day_strtotime = strtotime($date->format('Y-m-d H:i:s'));
        //Generating JWTs https://marketplace.zoom.us/docs/guides/auth/jwt
        $header = [
            'alg' => 'HS256',
            'typ' => 'JWT',
        ];
        $payload = [
            'iss' => $this->apikey,
            'exp' => $day_strtotime,
        ];

        $base64header = base64_encode(json_encode($header));
        $base64payload = base64_encode(json_encode($payload));
        $response_binary = hash_hmac('SHA256',
                                      $base64header.'.'.$base64payload,
                                      $this->apiSecret, true);
        $base64_signature = base64_encode($response_binary);
        $token = $base64header.'.'.$base64payload.'.'.$base64_signature;

        return $token;
    }
}
