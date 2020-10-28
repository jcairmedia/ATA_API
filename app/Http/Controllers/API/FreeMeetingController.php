<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Meetings\MeetingFreeRequest;
use App\Main\Config_System\Domain\SearchConfigDomain;
use App\Main\Config_System\UseCases\SearchConfigurationUseCase;
use App\Main\Contact\Domain\ContactCreatorDomain;
use App\Main\Contact\Domain\ContactSelectDomain;
use App\Main\Contact\UseCases\ContactFindUseCase;
use App\Main\Contact\UseCases\ContactRegisterUseCase;
use App\Main\Meetings\UseCases\MeetingRegisterUseCase;
use App\Utils\CustomMailer\EmailData;
use App\Utils\CustomMailer\MailLib;
use App\Utils\DateUtil;
use App\Utils\SMSUtil;
class FreeMeetingController extends Controller
{
    /**
     * @OA\Post(
     *  path="/api/meeting/free",
     *  summary="Registrar una cita gratuita",
     *  @OA\RequestBody(
     *   required=true ,
     *   description="Registrar una cita gratuita",
     *   @OA\JsonContent(
     *    required={"name","email","phone","date","time","type_meeting", "category"},
     *    @OA\Property(property="name", type="string", format="string", example="Nombres"),
     *    @OA\Property(property="email", type="string", format="email", example="user1@mail.com"),
     *    @OA\Property(property="phone", type="string", pattern="[0-9]{10}", format="number", example="1234567890"),
     *    @OA\Property(property="date", type="string", format="date", example="2020-10-26"),
     *    @OA\Property(property="time", type="string", format="string", example="18:00", pattern="/^(09|(1[0-8]))\:[0-5][0-9]$/"),
     *   )
     *  ),
     *  @OA\Response(
     *    response=201,
     *    description="Created",
     *    @OA\JsonContent(
     *      @OA\Property(
     *        property="code",
     *        type="int",
     *        example="201"
     *      ),
     *      @OA\Property(
     *        property="message",
     *        type="string",
     *        example="Cita creada"
     *      ),
     *      @OA\Property(
     *        property="data",
     *        type="array",
     *        collectionFormat="multi",
     *        @OA\Items(
     *            type="object",
     *            @OA\Property(property="folio", type="string", example="261020201320595f97219b21381"),
     *            @OA\Property(property="category", type="string", example="FREE"),
     *            @OA\Property(property="type_meeting", type="number", example="CALL"),
     *            @OA\Property(property="url_meeting", type="number", example=""),
     *            @OA\Property(property="dt_start", type="string", format="date-time", example="2020-10-26 18:40:00"),
     *            @OA\Property(property="dt_end", type="string", format="date-time", example="2020-10-26 19:20:00"),
     *            @OA\Property(property="price", type="number", example="1650.50"),
     *            @OA\Property(property="contacts_id", type="number", example="1"),
     *            @OA\Property(property="updated_at", type="number", example="1"),
     *            @OA\Property(property="created_at", type="number", example="1"),
     *          )
     *      )
     *    )),
     *   @OA\Response(
     *    response=422,
     *    description="Unprocessable Entity",
     *    @OA\JsonContent(
     *      @OA\Property(
     *        property="code",
     *        type="int",
     *        example="422"
     *      ),
     *      @OA\Property(
     *        property="message",
     *        type="string",
     *        example="The given data was invalid."
     *      ),
     *      @OA\Property(
     *        property="errors",
     *        type="object",
     *        @OA\Property(
     *            property="email",
     *            type="array",
     *            collectionFormat="multi",
     *             @OA\Items(type="string", example="El campo email es obligatorio.")
     *        )
     *      )
     *    )
     *  )
     * )
     */
    public function register(MeetingFreeRequest $request)
    {
        $contact_id = 0;
        try {
            $data = $request->all();
            $data['category'] = "FREE";
            $data['type_meeting']= 'CALL';
            // TODO: Verific datetime  handle

            // TODO: Create meeting in calendar

            // Search duration meeting
            $val_search_config = '';
            $CONFIG_PHONE_OFFICE = 'PHONE_OFFICE';
            $val_search_config = 'MEETING_FREE';
            $searchconfusecase = new SearchConfigurationUseCase(new SearchConfigDomain());
            $config = $searchconfusecase($val_search_config);
            // Contact
            try {
                $contactUseCase = new ContactRegisterUseCase(new ContactCreatorDomain());
                // Register contact
                $contact = $contactUseCase([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'phone' => $data['phone'],
                    ]);
                $contact_id = $contact->id;
            } catch (\Exception $ex) {
                // Search contact
                $contactUseCase = new ContactFindUseCase(new ContactSelectDomain());
                $contact = $contactUseCase($data['email']);
                $contact_id = $contact->id;
            }

            // Meeting
            $meetingUseCase = new MeetingRegisterUseCase();
            $meetingObj = $meetingUseCase($data, $contact_id, $config->value);

            // Send SMS
            $phone = $data['phone'];
            $config = $this->searchConfig($CONFIG_PHONE_OFFICE);

            $dateUtil = new DateUtil();
            $date = $data['date'];
            $day = $dateUtil->getDayByDate($date);
            $month = $dateUtil->getNameMonthByDate($date);

            $time = $data['time'];
            $textMsg = $this->createTextMsg($day, $month, $time, $config->value);
            $smsUtil = new SMSUtil();
            $smsUtil->__invoke($textMsg, $phone);

            // Send Email
            $view = $this->getViewEmail([
                'phone_office' => $config->value,
                'day' => $day,
                'month' => $month,
                'hours' => $time
            ]);

            $this->sendEmail($data['email'], "ATA | Cita Gratuita",'', $view);

            return response()->json([
                'code' => 201,
                'message' => 'Cita creada',
                'data' => $meetingObj->toArray(),
            ], 201);
        } catch (\Exception $ex) {
            $code = (int) $ex->getCode();
            if (!(($code >= 400 && $code <= 422) || ($code >= 500 && $code <= 503))) {
                $code = 500;
            }

            return response()->json([
                'code' => (int) $ex->getCode(),
                'message' => $ex->getMessage(),
        ], $code);
        }
    }

    private function searchConfig(string $val_search_config)
    {
        $searchconfusecase = new SearchConfigurationUseCase(new SearchConfigDomain());
        $config = $searchconfusecase($val_search_config);

        return $config;
    }

    private function sendEmail($email_customer, $subject, $bodyText, $bodyHtml)
    {
        $emailData = new EmailData(
            (object) ['email' => 'atanoreplay@gmail.com'],
            [$email_customer],
            $subject,
            $bodyText,
            $bodyHtml
        );

        try {
            $maillib = new MailLib([]);
            $maillib->Send($emailData);
        } catch (\Exception $ex) {
            \Log::error($ex->getMessage());
        }
    }

    private function createTextMsg($day, $month, $time, $phone_office)
    {
        $textMsg = 'Gracias por contactar a Abogados a Tu Alcance\n
        Agendate una guía gratuita para el día '.$day.' de '. $month.
        ' a las '.$time.'\nRecuerda comunicarte en la
        hora antes mencionada al télefono '.$phone_office;

        return $textMsg;
    }

    private function getViewEmail($array){
        $view = view('layout_meeting_Free',
                $array);
        return $view;
    }


}
