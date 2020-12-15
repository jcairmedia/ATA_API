<?php

namespace App\Main\OpenpayWebhookEvent\Services;

use GuzzleHttp\Client;

class GetStatusSubscriptionServices
{
    public function __invoke($customerId, $subscriptionId)
    {
        $client = new Client(['http_errors' => false]);
        try {
            $response = $client->request(
                'GET',
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

            $json_response = (string) $response->getBody();
            if ($statusCode != 200) {
                throw new \Exception('service open pay: '.$response->getReasonPhrase().(json_decode($json_response, true)['description']), $statusCode);
            }

            return $json_response;
        } catch (\RequestException  $e) {
            \Log::error('SERVICE: '.$e->getMessage(), $e->getCode());
            throw new \Exception($e->getMessage());
        }
    }
}
