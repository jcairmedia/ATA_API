<?php

namespace App\Main\Meetings\UseCases;

use App\CalendarEventMeeting;
use App\Main\CalendarEventMeeting\Domain\AddEventDomain;
use App\Main\CalendarEventMeeting\Domain\GetEventDomain;
use App\Main\Date\CaseUses\IsEnabledHourCaseUse;
use App\Main\Meetings\Domain\MeetingCreatorDomain;
use App\Main\Meetings\Domain\MeetingWhereDomain;
use App\Main\Meetings\Domain\MeetingWithContactDomain;
use App\Main\Scheduler\Domain\SearchSchedulerDomain;
use Carbon\Carbon;
use Spatie\GoogleCalendar\Event;

class MeetingReSchedulerUseCase
{
    public function __construct()
    {
    }

    public function __invoke(array $data, string $idCalendar, int $numberPlaces)
    {
        $meetingId = $data['meetingId'];

        $meetingArray = (new MeetingWhereDomain())(['id' => $data['meetingId']]);

        if (count($meetingArray) <= 0) {
            throw new \Exception('Cita no encontrada', 500);
        }
        $meetingObj = $meetingArray[0];
        // 1. is enabled hour in Calendar
        $rangeHour = $this->isValidDateInCalendar($data, $meetingObj, $idCalendar, $numberPlaces);

        $this->validateDate($data, $meetingObj->dt_start);

        // Obtener el id del calendario para actualizar fecha en calendar
        $event = (new GetEventDomain())($meetingObj->id);
        if ($event == null) {
            $meetingObjWithContact = (new MeetingWithContactDomain())($meetingObj->id);
            $event = new Event();
            $eventResult = $event->create(
                [
                    'name' => 'Llamar a '.$meetingObjWithContact->contact,
                    'description' => $this->setTextSubjectEventInCalendar($meetingObj->type_meeting),
                    'startDateTime' => new Carbon($data['date'].' '.$rangeHour->start),
                    'endDateTime' => new Carbon($data['date'].' '.$rangeHour->end), ],
                    $idCalendar
            );
            try {
                $calendar = new AddEventDomain();
                $calendar(new CalendarEventMeeting([
                    'meetings_id' => $meetingObjWithContact->id,
                    'idevent' => $eventResult->id,
                    'idcalendar' => $idCalendar, ]));
            } catch (\Exception $ex) {
                \Log::error('Error add Event in DB: '.$ex->getMessage());
            }
            $meetingObj->dt_start_rescheduler = $data['date'].' '.$rangeHour->start;
            $meetingObj->dt_end_rescheduler = $data['date'].' '.$rangeHour->end;
            $meetingObjNew = (new MeetingCreatorDomain())($meetingObj);

            return $meetingObjNew;
        }
        $_idEvent_ = $event->idevent;
        $_idCalendar_ = $event->idcalendar;
        $eventObj = Event::find($_idEvent_, $_idCalendar_);

        $eventObj->startDateTime = new Carbon($data['date'].' '.$rangeHour->start);
        $eventObj->endDateTime = new Carbon($data['date'].' '.$rangeHour->end);
        $eventObj->summary .= ' (re agendada)';

        $meetingObj->dt_start_rescheduler = $data['date'].' '.$rangeHour->start;
        $meetingObj->dt_end_rescheduler = $data['date'].' '.$rangeHour->end;
        $meetingObjUpdate = (new MeetingCreatorDomain())($meetingObj);
        $eventObj->save();

        return $meetingObjUpdate;
    }

    private function isValidDateInCalendar($data, $meetingObj, $idCalendar, $numberPlaces)
    {
        if ($meetingObj->dt_start_rescheduler != null) {
            throw new \Exception('La cita ya ha sido re agendada.', 409);
        }
        $n = new IsEnabledHourCaseUse();
        $isEnableHour = $n(
             $data['date'],
             $data['time'],
             'PAID',
             $idCalendar,
             $numberPlaces
         );
        if (!$isEnableHour) {
            throw new \Exception('Horario no disponible', 409);
        }

        // // Exist hour in work's scheduler
        $scheduler = new SearchSchedulerDomain();
        $rangeHour = $scheduler->_searchRangeHour($data['time'], 'PAID');
        if ($rangeHour == null) {
            throw new \Exception('Horario inválido', 409);
        }
        // // State paid
        if ($meetingObj->paid_state == 0) {
            throw new \Exception('La cita no se puede reagendar por falta de pago', 409);
        }

        // // Overdue Meeting
        if ($meetingObj->dt_cancellation != null) {
            throw new \Exception('No se puede reagendar la cita porque está cancelada', 409);
        }
        // close : 0; open :1
        if ($meetingObj->record_state == 0) {
            throw new \Exception('La cita ya fue atendida', 409);
        }

        return $rangeHour;
    }

    private function validateDate($data, $dateStartScheduled)
    {
        $now = new \DateTime();
        // $scheduled = new \DateTime($meetingObj->dt_start);
        $scheduled = new \DateTime($dateStartScheduled);
        $toSchedule = new \DateTime($data['date'].' '.$data['time']);
        $diff_ = $scheduled->diff($now);
        if ($diff_->invert == 0) {
            throw new \Exception('No puede reagendar porque su cita ya venció', 409);
        }
        if (($diff_->days * 24 + $diff_->h) < 24) {
            throw new \Exception('No puede reagendar con menor a 24 horas de anticipación', 500);
        }
        $diff_2 = $toSchedule->diff($now);
        // fecha de reagenda es menor  a hoy
        if ($diff_2->invert == 0) {
            throw new \Exception('La fecha seleccionada esta en el pasado', 409);
        }
        if (($diff_2->days * 24 + $diff_2->h) <= 1) {
            throw new \Exception('La fecha seleccionada esta en el pasado', 409);
        }

        return true;
    }

    private function setTextSubjectEventInCalendar($type_meeting)
    {
        $text = '';
        switch ($type_meeting) {
            case 'CALL':
                $text .= 'Tipo de cita: llamada';
            break;
            case 'VIDEOCALL':
                $text .= 'Tipo de cita: videollada';
                break;
            case 'PRESENTIAL':
                $text .= 'Tipo de cita: presencial';
            break;
        }

        return $text;
    }
}
