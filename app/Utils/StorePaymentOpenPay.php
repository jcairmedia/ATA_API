<?php

namespace App\Utils;

use GuzzleHttp\Client;

class StorePaymentOpenPay
{
    /**
     * Create Carge.
     *
     * @param [type] $chargeData
     *                           Properties ChargeData are
     *                           $customer = array(
     *                           'name' => 'Prueba con expiracion',
     *                           'phone_number' => '4423456723',
     *                           'email' => 'erika@airmedia.com.mx');
     *
     * $chargeData = array(
     *   'method' => 'store',
     *   'amount' => {monto},
     *   'description' => 'Cargo con expiración',
     *   'due_date' => $due,
     *   'customer'=> $customer
     * );
     *
     * @return void
     *              NOTE: for date due
     *              $datetime_due = new \DateTime('2020-11-01');
     *              $due = $datetime_due->format(\DateTime::ISO8601);
     */
    public function __invoke($chargeData)
    {
        $client = new Client(['http_errors' => false]);
        try {
            $response = $client->request(
                'POST',
                env('OPEN_PAY_ENVIROMENT').env('OPENPAY_ID').'/charges', [
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
