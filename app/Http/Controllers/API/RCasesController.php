<?php

namespace App\Http\Controllers\API;

use App\Cases;
use App\Http\Controllers\Controller;
use App\Http\Requests\Packages\SetLawyerRequest;
use App\Main\Cases\CasesUses\CreatePDFContractCaseUse;
use App\Main\Cases\CasesUses\ListCaseCaseUses;
use App\Main\Cases\CasesUses\TemplateContractCasesUse;
use App\Main\Cases\Domain\CaseInnerJoinCustomerDomain;
use App\Main\Users\Domain\FindUserByIdDomain;
use App\Utils\SendEmail;
use App\Utils\SMSUtil;
use Illuminate\Http\Request;

class RCasesController extends Controller
{
    public function list(Request $request)
    {
        $index = $request->input('index') ?? 0;
        $filter = $request->input('filter') ?? '';
        $byPage = $request->input('byPage') ?? 10;
        $caseUse = new ListCaseCaseUses();

        return response()->json($caseUse($filter, $index, $byPage, ['cases.state_paid_opening' => 0]));
    }

    public function setLawyer(SetLawyerRequest $request)
    {
        try {
            // Search Case an Update( set lawyer)
            $caseId = (int) $request->input('caseId');
            $userId = (int) $request->input('userId'); // Id user lawyer
            $case = Cases::where(['id' => $caseId])->first();
            if ($case == null) {
                throw new \Exception('Caso no encontrado', 500);
            }
            $lawyer = (new FindUserByIdDomain())(['id' => $userId]);
            // Search Case
            $obj = (new CaseInnerJoinCustomerDomain())(['cases.id' => $caseId]);
            // Generate Contract
            /*            $view = (new TemplateContractCasesUse())($obj);
                        $namefile = preg_replace('/[^A-Za-z0-9\-]/', '', uniqid($obj->packages_id.$obj->services_id.$obj->customer_id.date('Ymdhis'))).'.pdf';

                        // Create and save PDF
                        (new CreatePDFContractCaseUse())($view['layout'], $namefile, storage_path('contracts/'));
            */
            // Send Email
            $this->sendEmail($lawyer, $obj);
            // Send SMS
            $this->sendSMS($lawyer, $obj->customer_phone);

            // Update
            Cases::where(['id' => $caseId])->update(['users_id' => $userId]);

            return response()->json([
                'code' => 200,
                'message' => 'Abogado asociado al caso',
        ], 200);
        } catch (\Exception $ex) {
            \Log::error('Asociar rol al usuario: '.$ex->getMessage().$ex->getCode());

            return response()->json([
                    'code' => (int) $ex->getCode(),
                    'message' => $ex->getMessage(),
            ], (int) $ex->getCode());
        }
    }

    private function sendEmail($lawyer, $obj)
    {
        $dataLayout = [
            'lawyer' => $lawyer->name,
            'phone' => $lawyer->phone,
            'email' => $lawyer->email,
        ];
        $layoutEmail = view('layout_asignacion_lawyer', $dataLayout)->render();
        try {
            (new SendEmail())(
            ['email' => env('EMAIL_FROM')],
            [$obj->customer_email],
            'ATA | Te hemos asignado abogado a tu caso',
            '',
            $layoutEmail,
            [storage_path('contracts/').$obj->url_doc]
        );
        } catch (\Exception $ex) {
            \Log::error('Error Email asignaci??n de abogado: '.print_r($ex->getMessage(), 1));
        }
    }

    private function sendSMS($lawyer, $customerPhone)
    {
        $sms = 'Gracias por confiar en ATA como tu elecci??n de acompa??amiento legal.'.
                "Tu caso lo llevar?? el/la Lic. {$lawyer->name}, al cu??l podr??s encontrar en el siguiente n??mero y correo:".
                "{$lawyer->phone}, {$lawyer->email}";
        try {
            (new SMSUtil())($sms, $customerPhone);
        } catch (\Exception $ex) {
            \Log::error('SMS asignaci??n de abogado: '.$ex->getMessage());
        }
    }
}
