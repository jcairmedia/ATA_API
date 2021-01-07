<?php

namespace App\Console\Commands;

use GuzzleHttp\Client;
use Illuminate\Console\Command;

class commandopenpay extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'openpay:api';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function cliente(Type $var = null)
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
                        'json' => [
                            'name' => 'Petra',
                            'email' => 'erika@airmedia.com.mx',
                            'requires_account' => false,
                        ],
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

    public function tarjeta(Type $var = null)
    {
        $client = new Client(['http_errors' => false]);
        try {
            $response = $client->request(
                'POST',
                env('OPEN_PAY_ENVIROMENT').env('OPENPAY_ID').
                    '/customers'.'/'.'ayogvelineqzibhbumsx'.'/cards',
                    [
                        'headers' => [
                            'Content-Type' => 'application/json',
                            'Accept' => 'application/json',
                        ],
                        'curl' => [CURLOPT_USERPWD => env('OPENPAY_KEY_PRIVATE').':'],
                        'json' => [
                            'token_id' => 'kcmxaa28lfdpckfw7qbg',
                            'device_session_id' => '6ocgrgUhBI5TygXhIxkReRsvSUyHSjtc',
                        ],
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

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->tarjeta();
    }
}
