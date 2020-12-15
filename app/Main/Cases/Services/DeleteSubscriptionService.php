<?php

namespace App\Main\Cases\Services;

use GuzzleHttp\Client;

class DeleteSubscriptionService
{
    public function __invoke($customerId, $subscriptionId)
    {
        $client = new Client(['http_errors' => false]);
        try {
            $response = $client->request(
                'DELETE',
                env('OPEN_PAY_ENVIROMENT').env('OPENPAY_ID').
                    '/customers'.'/'.$customerId.
                    '/subscriptions'.'/'.$subscriptionId,
                    [
                        'headers' => [
                            'Content-Type' => 'application/json',
                            'Accept' => 'application/json',
                        ],
                        'curl' => [CURLOPT_USERPWD => env('OPENPAY_KEY_PRIVATE').':'],
                    ]
                );
            $statusCode = (int) $response->getStatusCode();
            \Log::error('StatusCode: '.$statusCode);
            $json_response = (string) $response->getBody();
            if ($statusCode != 204 && $statusCode != 200) {
                \Log::error('response services cancell : '.$response->getReasonPhrase());

                throw new \Exception($response->getReasonPhrase().' '.(json_decode($json_response, true)['description']), $statusCode);
            }

            return $json_response;
        } catch (\RequestException  $e) {
            \Log::error('SERVICE: '.$e->getMessage(), $e->getCode());
            throw new \Exception($e->getMessage());
        }
    }
}
