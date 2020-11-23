<?php

namespace App\Console\Commands;

use App\Main\Date\CaseUses\IsEnabledHourCaseUse;
use App\Main\Scheduler\Domain\SearchSchedulerDomain;
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
        $event = new Event();
        $event->create(['name' => 'Cita free con Al',
         'startDateTime' => new Carbon('2020-11-27 10:21:00'),
         'description' => 'La descripcion de prueba',
         'addAttendee' => [
            'email' => 'john@example.com',
            'name' => 'John Doe',
            'comment' => 'Lorum ipsum', ],
         'endDateTime' => new Carbon('2020-11-27 11:00:00'), ],
         'io7n2prsu83uc8isfcke2eqnrg@group.calendar.google.com');

        /*$n = new IsEnabledHourCaseUse();
        $isEnableHour = $n('2020-11-21',
        '12:15:00',
        'PAID',
        'io7n2prsu83uc8isfcke2eqnrg@group.calendar.google.com', 2);
        */
        /*$event = new Event();
        $eventos = $event->get(new Carbon('2020-11-23 09:00:00'),
             new Carbon('2020-11-23 11:30:00'),[],
            'io7n2prsu83uc8isfcke2eqnrg@group.calendar.google.com');
        $array = [];
        foreach ($eventos as $key => $value) {
            $array[] = [
                'start' => $value->start->dateTime,
                'end' => $value->end->dateTime,
            ];
        }
        print_r($array);*/

        /*$scheduler = new SearchSchedulerDomain();
        $rangeHour = $scheduler->_searchRangeHour('12:15:00', 'PAID');
        if ($rangeHour == null) {
            throw new \Exception('No encotrado', 1);
        }
        print_r($rangeHour->toArray());*/

        // $event->name = 'A new full day event';
        // $event->startDate = Carbon::now();
        // $event->endDate = Carbon::now()->addDay();

        // $event->save();

        // $google = GoogleCalendarFactory
        // ::
        // createForCalendarId(env('GOOGLE_CALENDAR_ID'));
        // $list = $google->listEvents(
        //     new Carbon('2020-11-20 14:00:00'),
        //      new Carbon('2020-11-22 23:00:00'));

        // $this->line(print_r($list->items, true));

        return 0;
    }
}
