<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Spatie\GoogleCalendar\Event;
use Spatie\GoogleCalendar\GoogleCalendarFactory;

class testGoogle extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'google:test';

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
        // $event = new Event();

        // $event->name = 'A new full day event';
        // $event->startDate = Carbon::now();
        // $event->endDate = Carbon::now()->addDay();

        // $event->save();

        $google = GoogleCalendarFactory
        ::
        createForCalendarId(env('GOOGLE_CALENDAR_ID'));
        $list = $google->listEvents(
            new Carbon('2020-11-20 14:00:00'),
             new Carbon('2020-11-22 23:00:00'));

        $this->line(print_r($list->items, true));

        return 0;
    }
}
