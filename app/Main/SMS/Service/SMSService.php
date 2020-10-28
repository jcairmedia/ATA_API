<?php

namespace App\Main\SMS\Service;

use GuzzleHttp\Client;

class SMSService
{
    private $client = '';
    private $token = '';

    public function __construct($token)
    {
        $this->token = $token;
        $this->client = new Client(['http_errors' => false]);
    }

    /**
     * Send sms.
     *
     * @param string $message
     * @param string $number
     *
     * @return array
     */
    public function __invoke($message, $number)
    {
        $response = $this->client->request(
            'post',
            'https://api.mailjet.com/v4/sms-send', [
            'headers' => [
                'Authorization' => "Bearer {$this->token}",
                'Content-Type' => 'application/json',
            ],
            'body' => json_encode(['Text' => $message, 'To' => $number, 'From' => 'SEL']),
            ]);

        $statusCode = (int) $response->getStatusCode();
        $data = json_decode((string) $response->getBody());

        return [
            'statusCode' => $statusCode,
            'data' => $data,
        ];
    }
}
