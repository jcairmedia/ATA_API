<?php

namespace App\Main\Users\Services;

class SynchronizedUserExternalServices
{
    public function __invoke()
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => env('URL_STATE_SYNCHRONIZATION_USERS'),
            CURLOPT_RETURNTRANSFER => true,
        ]);
        $response = curl_exec($curl);

        $err = curl_error($curl);
        if ($err) {
            \Log::error('error: '.print_r($err, 1));
            throw new \Exception(print_r($err, 1));
        }
        curl_close($curl);

        $response = trim($response);

        return $response;
    }
}
