<?php

namespace App\Main\Subscription\Services;

use GuzzleHttp\Client;

class ContractPackagePaymentByCardCustomerOpenPayService
{
    public function __invoke(
        string $customerId,
        string $cardId,
        string $planId,
        string $trial_end_date)
    {
        try {
            $subscription = [
                'trial_end_date' => $trial_end_date,
                'plan_id' => $planId,
                'card_id' => $cardId,
            ];
            $client = new Client(['http_errors' => false]);

            $response = $client->request(
                'POST',
                env('OPEN_PAY_ENVIROMENT').env('OPENPAY_ID').
                    '/customers'.'/'.$customerId.'/subscriptions',
                    [
                        'headers' => [
                            'Content-Type' => 'application/json',
                            'Accept' => 'application/json',
                        ],
                        'curl' => [CURLOPT_USERPWD => env('OPENPAY_KEY_PRIVATE').':'],
                        'json' => $subscription,
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

            return [
                'customerId' => $customerId,
                'cardId' => $cardId,
                'json' => $json_response, ];
        } catch (\Exception $e) {
            \Log::error(__FILE__);
            \Log::error($e->getMessage());

            throw new \Exception($e->getMessage(), (int) $e->getCode());
        }
    }
}
