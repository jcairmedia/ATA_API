<?php

namespace App\Console\Commands;

use App\Utils\CustomMailer\EmailData;
use App\Utils\CustomMailer\MailLib;
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
        $emailData = new EmailData((object) ['email' => 'atanoreply@gmail.com'], ['erika@airmedia.com.mx'], 'Probando', 'Esto es un ejemplo');
        try {
            //code...
            $maillib = new MailLib([]);
            $maillib->Send($emailData);
        } catch (\Throwable $th) {
            \Log::error($th->getMessage());
        }
    }
}
