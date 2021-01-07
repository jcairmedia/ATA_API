<?php

namespace App\Main\OpenPayCustomerCards\Services;

use GuzzleHttp\Client;

class AddCustomerCardOpenPayService
{
    public function __invoke($json, $idOpenpayCustomer)
    {
        $client = new Client(['http_errors' => false]);
        try {
            $response = $client->request(
                'POST',
                env('OPEN_PAY_ENVIROMENT').env('OPENPAY_ID').
                    '/customers'.'/'.$idOpenpayCustomer.'/cards',
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
            $jsonResponse = (string) $response->getBody();
            if ($statusCode != 201 && $statusCode != 200) {
                \Log::error('response services cancell : '.$response->getReasonPhrase());

                throw new \Exception($response->getReasonPhrase().' '.(json_decode($jsonResponse, true)['description']), $statusCode);
            }
            \Log::error('data: '.print_r($jsonResponse, 1));

            return $jsonResponse;
        } catch (\RequestException  $e) {
            \Log::error('SERVICE: '.$e->getMessage(), $e->getCode());
            throw new \Exception($e->getMessage());
        }
    }
}
