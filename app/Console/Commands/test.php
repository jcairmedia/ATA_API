<?php

namespace App\Console\Commands;

use App\Utils\CustomMailer\EmailData;
use App\Utils\CustomMailer\MailLib;
use GuzzleHttp\Client;
use Illuminate\Console\Command;

class test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:api';

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
        /*
        $path_file = storage_path('app'.DIRECTORY_SEPARATOR.'infraestructure').DIRECTORY_SEPARATOR.env('GOOGLE_SERVICE_ACCOUNT_FILE_NAME');

        $this->line($path_file);

        $client = new \Google_Client();
        $client->setAuthConfig($path_file);

        $client->setApplicationName('ATA-API-DEV');
        $client->setScopes([\Google_Service_Calendar::CALENDAR]);
        $client->setSubject('erika@airmedia.com.mx');
        $service = new \Google_Service_Calendar($client);

        $calendarList = $service->calendarList->listCalendarList();

        $calendarios = $calendarList->getItems();
        foreach ($calendarios as $key => $value) {
            $this->line($value->getSummary());
        }
        // Print the next 10 events on the user's calendar.
        $calendarId = 'primary';
        $optParams = [
                        'maxResults' => 10,
                        'orderBy' => 'startTime',
                        'singleEvents' => true,
                        'timeMin' => date('c'),
                        ];
        $results = $service->events->listEvents($calendarId, $optParams);
        $events = $results->getItems();

        $this->line(print_r($events, 1));
        */
        // $emailData = new EmailData((object) ['email' => 'atanoreply@gmail.com'], ['erika@airmedia.com.mx'], 'Probando', 'Esto es un ejemplo');
        // try {
        //     //code...
        //     $maillib = new MailLib([]);
        //     $maillib->Send($emailData);
        // } catch (\Throwable $th) {
        //     \Log::error($th->getMessage());
        // }

        // $openpay = \Openpay::getInstance(env('OPENPAY_ID'), env('OPENPAY_KEY_PRIVATE'));
        // $customer = array(
        //     'name' => 'Juan Vazquez',
        //     'phone_number' => '4423456723',
        //     'email' => 'erika@airmedia.com.mx');

        // $chargeData = array(
        //     'method' => 'store',
        //     'amount' => 100.00,
        //     'description' => 'Cargo a tienda',
        //     'customer'=> $customer
        // );

        // $charge = $openpay->charges->create($chargeData);
        // \Log::error(print_r($charge,true));
        // $this->line(json_encode($charge, JSON_PRETTY_PRINT));

        /*$customer = [
            'name' => 'Prueba con expiracion',
            'phone_number' => '4423456723',
            'email' => 'erika@airmedia.com.mx', ];
        $datetime_due = new \DateTime('2020-11-01');
        $due = $datetime_due->format(\DateTime::ISO8601);

        $chargeData = [
            'method' => 'store',
            'amount' => 50.00,
            'description' => 'Cargo con expiración',
            'due_date' => $due,
            'customer' => $customer,
        ];

        $client = new Client(['http_errors' => false]);

        $response = $client->request(
            'post',
            'https://sandbox-api.openpay.mx/v1/'.env('OPENPAY_ID').'/charges', [
            'headers' => [
                // 'Authorization' => "Bearer {$this->token}",
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'body' => json_encode($chargeData),
            'curl' => [CURLOPT_USERPWD => env('OPENPAY_KEY_PRIVATE').':'],
            ]);

        $statusCode = (int) $response->getStatusCode();
        $json_response = (string) $response->getBody();
        \Log::error($json_response);
        // $this->line($json_response['authorization']);
        $response = json_decode($json_response, true);
        $this->line($response['payment_method']['type']);
        $this->line($json_response);*/

        // eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJhdWQiOm51bGwsImlzcyI6IlRwVDl1U2xEVDV1LUJFLWduMU9EOUEiLCJleHAiOjE2MDQ2MzQ5OTMsImlhdCI6MTYwNDAzMDE5NH0.VJNWZ_rykYtblUhGNwPAhMORlVHmtukVXRGn-MUw5EY
        /*$curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.zoom.us/v2/users?status=active&page_size=30&page_number=1',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => [
                'authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJhdWQiOm51bGwsImlzcyI6IlRwVDl1U2xEVDV1LUJFLWduMU9EOUEiLCJleHAiOjE2MDQ2MzQ5OTMsImlhdCI6MTYwNDAzMDE5NH0.VJNWZ_rykYtblUhGNwPAhMORlVHmtukVXRGn-MUw5EY',
                'content-type: application/json',
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo 'cURL Error #:'.$err;
        } else {
            echo $response;
        }*/

        /*
            // GENERANDO UN MEETING
        $date_utc = new \DateTime("30-10-2020 11:30");
        $this->line($date_utc->format('Y-m-d\\TH:i:s\\Z'));
        $date_utc->setTimezone( new \DateTimeZone("UTC"));
        $date = $date_utc->format('Y-m-d\\TH:i:s\\Z');


        $curl = curl_init();
        $data = [
            "start_time" => $date,
            "timezone" => "UTC",
            "topic" => "Reunioon de avances",
            "settings" => [
              "host_video"=> true,
              "participant_video"=> true,
            ]
        ];
        $StringData = json_encode($data);

        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.zoom.us/v2/users/iamdleonor@gmail.com/meetings',
            CURLOPT_SSL_VERIFYPEER => 1,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJUcFQ5dVNsRFQ1dS1CRS1nbjFPRDlBIiwiZXhwIjoxNjA0MDk3MDAwfQ==.gOHVaAxvdx6ZfsTNX47dN9xvTDAyR+jFFrQKszAZ7ZQ=',
                'content-Type: application/json',
                'Content-length: '.strlen($StringData),

            ],
            CURLOPT_POSTFIELDS => $StringData
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo 'cURL Error #:'.$err;
        } else {
            echo print_r($response);
            $json = json_decode($response, true);
            echo "url: ". $json['join_url'];
            echo "pass: ". $json['password'];
        }*/

        /*
            // GENERANDO UN JWT
        $date_utc = new \DateTime("30-10-2020 16:30");
        $header = [
            "alg" => 'HS256',
            "typ" => "JWT"

        ];
        $payload = [
            "iss"=> "TpT9uSlDT5u-BE-gn1OD9A",
            "exp" =>strtotime("30-10-2020 16:30")
        ];
        $base64header = base64_encode(json_encode($header));
        $base64payload = base64_encode(json_encode($payload));
        $response_binary = hash_hmac('SHA256',
        $base64header. '.'. $base64payload,
        'igSxyF4Dgtjg0zfSsF6EHggpyaiaTruVt9yA'
        , true);
        $base64_signature = base64_encode($response_binary);

        $this->line($base64header. '.'. $base64payload. '.'.$base64_signature);*/

        $date = new \DateTime('2020-11-03');

        // $this->line($date->format('Y-m-d H:i:s'));
        // $date->modify('+24 hours');
        // $this->line($date->format('Y-m-d H:i:s'));
        // echo($date->format('Y-m-d H:i:s'))."\n";
        // $now = new \DateTime();
        // $this->line(print_r($now, 1));
        // $this->line(print_r($date, 1));
        // $interval = $now->diff($date);
        // $this->line(print_r($interval, 1));

        // if ((int) $interval->invert == 1) {
        //     $this->error('Fecha pasada invalida');
        // }
        // if (($interval->days < 0 || $interval->days >= 26)) {
        //     $this->error('No se pueden realizar citas con más de 25 días o menos días a la fecha actual');
        // }

        // invert 1 => fecha pasada
        // 0 <= days <=26 => fecha valida
        // sabado <= day <= domingo => fecha invalida

        // $dia_semana = (int) $date->format('N');
        // if ($dia_semana >= 6) {
        //     $this->error('No se realizan citas los fines de semana');
        // }
    }
}
