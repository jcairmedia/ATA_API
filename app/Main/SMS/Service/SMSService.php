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
     *
     * @return array
     */
    public function __invoke($message, $phone)
    {
        $phone = '+52'.$phone;
        $response = $this->client->request(
            'POST',
            'https://api.mailjet.com/v4/sms-send', [
            'headers' => [
                'Authorization' => "Bearer {$this->token}",
                'Content-Type' => 'application/json',
            ],
            'body' => json_encode(['Text' => $message, 'To' => $phone, 'From' => 'SEL']),
            ]);

        $statusCode = (int) $response->getStatusCode();
        $stringResponse = (string) $response->getBody();
        $data = json_decode((string) $response->getBody());
        \Log::error('Msn: '.$message);
        \Log::error('phone: '.$phone);
        \Log::error('token: '.$this->token);
        \Log::error((__FILE__.' error en el envio de SMS'.$stringResponse));

        return [
            'statusCode' => $statusCode,
            'data' => $data,
        ];
    }
}
