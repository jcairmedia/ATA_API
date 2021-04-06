<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class testConnectCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'connect:ata';

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

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $dataForm = ['grant_type' => 'password',
        'client_id' => '2',
        'client_secret' => '6TwmVwNV6DgkrR05RBJx8uLlNLfgm5k5FB4TLlZE',
        'username' => 'avalle@actin.com.mx',
        'password' => 'vX#M^yp%KcIy8',
        'scope' => '*', ];

        // PASO 1. Autenticación
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://apiqa.usercenter.mx/oauth/token',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $dataForm,
        ]);
        $response = curl_exec($curl);

        $err = curl_error($curl);
        if ($err) {
            // echo 'cURL Error #:'.$err;
            throw new \Exception(print_r($err, 1));
        }
        curl_close($curl);

        $json = json_decode($response, true);
        $jwt = $json['access_token'];
        //Paso 2. Consulta

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://apiqa.usercenter.mx/api/relate/news/users',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer '.$jwt,
                'content-Type: application/json',
            ],
        ]);

        $response2 = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        $json2 = json_decode($response2, true);

        print_r($json2);
    }
}
