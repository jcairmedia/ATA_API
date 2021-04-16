<?php

namespace App\Main\Meetings\UseCases;

use App\CalendarEventMeeting;
use App\Main\CalendarEventMeeting\CaseUses\CreateEventInDBCaseUse;
use App\Main\CalendarEventMeeting\Domain\AddEventDomain;
use App\Main\Date\CaseUses\IsEnabledHourCaseUse;
use App\Main\Evidencesmeetings\CaseUses\CreateEvidenceMeetingCaseUse;
use App\Main\Scheduler\Domain\SearchSchedulerDomain;
use Carbon\Carbon;
use Spatie\GoogleCalendar\Event;

class MeetingTransferPaidUseCase
{
    public function __construct(MeetingRegisterUseCase $meetingUseCase)
    {
        $this->meetingUseCase = $meetingUseCase;
        $this->event = new Event();
    }

    public function __invoke($data, $userObj,
        $phoneOffice,
        $durationMeeting,
        $amountPaid,
        $idCalendar,
        $numberPlaces,
        $bankAccount)
    {
        // Validar si la fecha que se esta proporcionando es valida
        // para asesorias tipo de pago transaction

        $n = new IsEnabledHourCaseUse();
        $isEnableHour = $n(
                $data['date'],
                $data['time'],
                'PAID',
                $idCalendar,
                $numberPlaces
            );
        if (!$isEnableHour) {
            throw new \Exception('Hora no disponible', 400);
        }
        $scheduler = new SearchSchedulerDomain();
        $rangeHour = $scheduler->_searchRangeHour($data['time'], 'PAID');
        if ($rangeHour == null) {
            throw new \Exception('Horario no encontrado');
        }
        $dtStart = ($data['date'].' '.$rangeHour->start);
        $dtEnd = ($data['date'].' '.$rangeHour->end);

        // Add event in Calendar
        $eventResult = $this->event->create(
            [
                'name' => 'Llamar a '.$userObj->name.' '.$userObj->last_name1.' '.$userObj->last_name2,
                'description' => $this->setTextSubjectEventInCalendar($data['type_meeting']),
                'startDateTime' => new Carbon($dtStart),
                'endDateTime' => new Carbon($dtEnd), ],
                $idCalendar
        );

        // Save Meeting in DB
        $data['amount'] = $amountPaid;
        $data['category'] = 'PAID';
        $data['paid'] = 0;
        if (!array_key_exists('description', $data)) {
            $data['description'] = '';
        }

        $meetingObj = $this->meetingUseCase->__invoke($data, 0, $durationMeeting, $userObj->id);

        // Add event in DB
        (new CreateEventInDBCaseUse())($meetingObj->id, $eventResult->id, $idCalendar);
        // $this->saveEventInDB($meetingObj->id, $eventResult->id, $idCalendar);

        // Save Folio evidence
        $evidenceNew = (new CreateEvidenceMeetingCaseUse())($meetingObj->id);
        // TODO: Enviar correo electronico
        //TODO: Falta enviar url donde el usuario debe de subir su evidencia
        return $evidenceNew;
    }

    private function saveEventInDB($meetingId, $eventId, $idCalendar)
    {
        try {
            $calendar = new AddEventDomain();
            $calendar(new CalendarEventMeeting([
            'meetings_id' => $meetingId,
            'idevent' => $eventId,
            'idcalendar' => $idCalendar, ]));
        } catch (\Exception $ex) {
            \Log::error('ErrorOfflineAddEvent: '.$ex->getMessage());
        }
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
