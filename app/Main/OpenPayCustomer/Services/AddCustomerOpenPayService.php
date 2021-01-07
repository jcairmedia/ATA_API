<?php

namespace App\Main\OpenPayCustomer\Services;

use GuzzleHttp\Client;

class AddCustomerOpenPayService
{
    public function __invoke($json)
    {
        $client = new Client(['http_errors' => false]);
        try {
            $response = $client->request(
                'POST',
                env('OPEN_PAY_ENVIROMENT').env('OPENPAY_ID').
                    '/customers',
                    [
                        'headers' => [
                            'Content-Type' => 'application/json',
                            'Accept' => 'application/json',
                        ],
                        'curl' => [CURLOPT_USERPWD => env('OPENPAY_KEY_PRIVATE').':'],
                        'json' => $json,
                    ]
                );

            $statusCode = (int) $response->getStatusCode();
            \Log::error('StatusCode: '.$statusCode);
            $json_response = (string) $response->getBody();
            if ($statusCode != 201 && $statusCode != 200) {
                \Log::error('response services cancell : '.$response->getReasonPhrase());

                throw new \Exception($response->getReasonPhrase().' '.(json_decode($json_response, true)['description']), $statusCode);
            }
            \Log::error('data: '.print_r($json_response, 1));

            return $json_response;
        } catch (\RequestException  $e) {
            \Log::error('SERVICE: '.$e->getMessage(), $e->getCode());
            throw new \Exception($e->getMessage());
        }
    }
}
