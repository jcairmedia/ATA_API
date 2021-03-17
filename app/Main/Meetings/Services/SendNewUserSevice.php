<?php

namespace App\Main\Meetings\Services;

use GuzzleHttp\Client;

class SendNewUserService
{
    public function __invoke(array $data)
    {
        $client = new Client(['http_errors' => false]);
        try {
            $response = $client->request(
                'POST',
                env('URL_SOFTWARE_ATA'), [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'body' => json_encode($data),
                ]);
            $statusCode = (int) $response->getStatusCode();

            $json_response = (string) $response->getBody();
            if ($statusCode != 200) {
                throw new \Exception('URL_SOFTWARE_ATA: '.$response->getReasonPhrase().(json_decode($json_response, true)['description']), $statusCode);
            }

            return $json_response;
        } catch (\RequestException  $e) {
            \Log::error('SERVICE: '.$e->getMessage(), $e->getCode());
            throw new \Exception($e->getMessage());
        } catch (\Exception $ex) {
            \Log::error('SERVICE: '.$e->getMessage(), $e->getCode());
            throw new \Exception($e->getMessage());
        }
    }
}
