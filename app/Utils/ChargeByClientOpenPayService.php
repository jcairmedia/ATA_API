<?php

namespace App\Utils;

use GuzzleHttp\Client;

class ChargeByClientOpenPayService
{
    /**
     * Create Carge by client.
     */
    public function __invoke($chargeData, $customerId)
    {
        $client = new Client(['http_errors' => false]);
        try {
            $response = $client->request(
                'POST',
                env('OPEN_PAY_ENVIROMENT').env('OPENPAY_ID').'/customers'.'/'.$customerId.'/charges', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'body' => json_encode($chargeData),
                'curl' => [CURLOPT_USERPWD => env('OPENPAY_KEY_PRIVATE').':'],
                ]);
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
