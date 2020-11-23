<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Meetings\PaidMeetingRequest;
use App\Main\Config_System\Domain\SearchConfigDomain;
use App\Main\Config_System\UseCases\SearchConfigurationUseCase;
use App\Main\Contact\Domain\ContactCreatorDomain;
use App\Main\Contact\Domain\ContactSelectDomain;
use App\Main\Contact\UseCases\ContactFindUseCase;
use App\Main\Contact\UseCases\ContactRegisterUseCase;
use App\Main\Meetings\UseCases\MeetingOffilePayment;
use App\Main\Meetings\UseCases\MeetingOnlinePayment;
use App\Main\Meetings\UseCases\MeetingRegisterUseCase;
use App\Main\Meetings_payments\Domain\PaymentDomain;
use App\Main\Meetings_payments\UseCases\RegisterPaymentUseCases;
use App\Main\OpenPay_payment_references\Domain\CreaterChargeDomain;
use App\Main\OpenPay_payment_references\UseCases\RegisterOpenPayChargeUseCase;
use App\Utils\StorePaymentOpenPay;

class PaidMeetingController extends Controller
{
    public function __construct()
    {
        $this->CONFIG_PHONE_OFFICE = 'PHONE_OFFICE';
        $this->CONFIG_MEETING_PAID_DURATION = 'MEETING_PAID_DURATION';
        $this->CONFIG_MEETING_PAID_AMOUNT = 'MEETING_PAID_AMOUNT';
    }

    public function register(PaidMeetingRequest $request)
    {
        $contact_id = 0;

        try {
            $data = $request->all();
            // Search duration meeting
            $searchconfusecase = new SearchConfigurationUseCase(new SearchConfigDomain());

            $searchconfusecase = new SearchConfigurationUseCase(new SearchConfigDomain());
            $response_CONFIG_PHONE_OFFICE = $searchconfusecase($this->CONFIG_PHONE_OFFICE);
            $response_CONFIG_MEETING_PAID_DURATION = $searchconfusecase($this->CONFIG_MEETING_PAID_DURATION);
            $response_CONFIG_MEETING_PAID_AMOUNT = $searchconfusecase($this->CONFIG_MEETING_PAID_AMOUNT);

            $PHONE_OFFICE = $response_CONFIG_PHONE_OFFICE->value;
            $MEETING_PAID_DURATION = $response_CONFIG_MEETING_PAID_DURATION->value;
            $MEETING_PAID_AMOUNT = $response_CONFIG_MEETING_PAID_AMOUNT->value;

            // invoke case use OFFLINE
            if ($data['type_payment'] == 'OFFLINE') {
                $meetingOffile = new MeetingOffilePayment(
                                    new StorePaymentOpenPay(),
                                    new MeetingRegisterUseCase(),
                                    new RegisterOpenPayChargeUseCase(new CreaterChargeDomain()),
                                    new ContactRegisterUseCase(new ContactCreatorDomain()),
                                    new ContactFindUseCase(new ContactSelectDomain()));

                $objectMeeting = $meetingOffile($data, $MEETING_PAID_DURATION, $PHONE_OFFICE, $MEETING_PAID_AMOUNT);
                \Log::error(print_r($objectMeeting, 1));

                return response()->json(['code' => 201, 'data' => $objectMeeting], 201);
            }
            // invoke case use ONLINE
            if ($data['type_payment'] == 'ONLINE') {
                $meeting_online = new MeetingOnlinePayment(
                    new StorePaymentOpenPay(),
                    new RegisterPaymentUseCases(new PaymentDomain()),
                    new MeetingRegisterUseCase(),
                    new ContactRegisterUseCase(new ContactCreatorDomain()),
                    new ContactFindUseCase(new ContactSelectDomain()));
                $objectMeeting = $meeting_online($data, $MEETING_PAID_AMOUNT, $MEETING_PAID_DURATION, $PHONE_OFFICE);

                return response()->json(['code' => 201, 'data' => $objectMeeting], 201);
            }
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
}
