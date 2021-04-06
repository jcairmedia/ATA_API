<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Main\Documents_Cases\Domain\CreateDocumentDomain;
use App\Main\Packages\Domain\WherePackageDomain;
use App\Main\Cases\Domain\SaveCaseDomain;
use App\Main\Cases\Domain\CaseInnerJoinCustomerDomain;
use App\Main\Cases\CasesUses\TemplateContractCasesUse;
use App\Main\Cases\CasesUses\CreatePDFContractCaseUse;
use App\Utils\SMSUtil;
use App\Utils\DateUtil;
use App\Utils\SendEmail;

use Illuminate\Http\Request;


class ContractPackageOfflineController extends Controller
{
    public function __construct()
    {
        $this->LAYOUT_CONTRACT_PACKAGES_OFFLINE = 'layout_contract_package_offline_first_payment';
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $data = $request->all();
        $nowDT = new \DateTime();
        try {
            // Buscar el paquete para obtener el identificador del plan
            $packageId = $data['packageId'];
            $arrayPackages = (new WherePackageDomain())(['id' => $packageId]);

            if (count($arrayPackages) <= 0) {
                throw new \Exception('Paquete no encontrado', 404);
            }
            $arrayPackages = $arrayPackages[0];
            $caseObj = (new SaveCaseDomain())([
                'folio' => preg_replace('/[^A-Za-z0-9\-\_]/', '', uniqid('', true)),
                'packages_id' => $data['packageId'],
                'url_doc' => '',
                'price' => $arrayPackages->amount,
                'services_id' => $data['serviceId'],
                'customer_id' => $user->id,
                'state_paid_opening' => 0, // pending
                'idfe' => $data['idfe'],
            ]);

            // save documents pending
            (new CreateDocumentDomain())([
                'folio' => preg_replace('/[^A-Za-z0-9\-\_]/', '', uniqid('', true)),
                'case_id' => $caseObj->id,
                'status' => 'UPLOAD_PENDING',
                'time_review' => (new \DateTime())->format('Y-m-d H:i:s'),
                'number_times_review' => 0,
            ]);

            // Search customer
            $obj = (new CaseInnerJoinCustomerDomain())(['cases.id' => $caseObj->id]);

            // Generate Contract
            $view = (new TemplateContractCasesUse())($obj);
            $namefile = preg_replace(
                '/[^A-Za-z0-9\-]/',
                '',
                uniqid(
                    $obj->packages_id.
                    $obj->services_id.
                    $obj->customer_id.
                    date('Ymdhis'))).
                    '.pdf';

            // Create and save PDF
            ini_set('max_execution_time', 5000);
            ini_set('memory_limit', '512M');

            (new CreatePDFContractCaseUse())($view['layout'], $namefile, storage_path('contracts/'));

            // Update package with URL_DOC

            $caseObj->url_doc = $namefile;
            $caseObj->save();

            // Enviar SMS
            $testSMS = $this->textSMS($arrayPackages->name);
            if ($user->phone != null) {
                (new SMSUtil())($testSMS, $user->phone);
            }

            // Enviar correo
            $DT_valid = new \DateTime(date('Y-m-d', strtotime(date('Y-m-d').'+1month-1day')));

            $dateUtil = new DateUtil();

            $month = $dateUtil->getNameMonth($nowDT->format('m'));
            $day = $nowDT->format('d');

            $month_valid = $dateUtil->getNameMonth($DT_valid->format('m'));
            $day_valid = $DT_valid->format('d');

            $view = view($this->LAYOUT_CONTRACT_PACKAGES_OFFLINE)->render();
            (new SendEmail())(
                 ['email' => env('EMAIL_FROM')],
                 [$user->email],
                 'Para pago del paquete '.$arrayPackages->name.' a contratar',
                 '',
                 $view
             );
            // response cliente
            return response()->json(['data' => $caseObj], 201);
        } catch (\Exception $ex) {
            \Log::error($ex->getMessage());
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

    //TODO: Change Message SMS
    public function textSMS($paquete)
    {
        return
        'Hemos recibido con éxito tu pago para nuestro'.
        ' servicio de asesoría legal en nuestro'.
        ' Paquete '.$paquete.'.'.
        ' Para dudas y aclaraciones comunícate al 55-2625-0649
        ';
    }
}
