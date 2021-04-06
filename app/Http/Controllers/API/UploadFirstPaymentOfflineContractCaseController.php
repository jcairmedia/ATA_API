<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Utils\FileUtils;
use App\Http\Requests\UploadDocForContractPackageRequest;
use App\Main\Documents_Cases\Domain\FindDocumentCaseDomain;

use Illuminate\Http\Request;

class UploadFirstPaymentOfflineContractCaseController extends Controller
{

    public function index(UploadDocForContractPackageRequest $request)
    {
        try{
            $folioEvidencia = $request->input('folio');

            $fileObj = $request->input('file');

            $fileInstance = new FileUtils($request);
            $fileInstance->read('file');
            $pathNameFile = $fileInstance->save('evidencia_contratacion_paquete', 'uno_prueba.png');

            // Buscar el id del documento del caso
            $evidenciaObj = (new FindDocumentCaseDomain())(['folio' => $folioEvidencia]);
            if(is_null($evidenciaObj)){
                throw new \Exception("No existe evidencia", 404);
            }

            if($evidenciaObj->status == "APPROVED"){ //&& $evidenciaObj->status != "NO_APPROVED"){
                throw new \Exception("La evidencia ya fue aprobada", 409);
            }

            if($evidenciaObj->status == "NO_APPROVED"){ //&& $evidenciaObj->status != "NO_APPROVED"){
                throw new \Exception("La evidencia no fue aprobada y su tiempo expiro", 409);
            }
            if ($evidenciaObj->number_times_review > 1){
                throw new \Exception("Numero de veces expirado", 409);
            }
            if($evidenciaObj->status == "IN_REVIEW_REVIEWER"){
                throw new \Exception("Espere a que terminen de revisar su evidencia", 409);
            }

            $evidenciaObj->url = $pathNameFile;
            $evidenciaObj->status = "IN_REVIEW_REVIEWER";
            $evidenciaObj->save();

            // TODO: Hay que enviar un email para avisar
            // que hay un nuevo doc que revisar
            return response()->json([
                'message' => 'Evidencia guardada, espere a que sea revisada.',
            ], 200);
        } catch (\Exception $ex) {
            \Log::error($ex->getMessage());
            $code = (int) $ex->getCode();
            // if (!(($code >= 400 && $code <= 422) || ($code >= 500 && $code <= 503))) {
            //     $code = 500;
            // }

            return response()->json([
                'code' => (int) $ex->getCode(),
                'message' => $ex->getMessage(),
        ], $code);
        }
    }
    // TODO: Falta probar
    public function evaluacion(Request $request)
    {
        try {
            $user = $request->user();
            $comment = $request->input("comment");
            $DocCommentId = $request->input("document_case_id");
            $status = $request->input("status");

            $evidenciaObj = (new FindDocumentCaseDomain())(['id' => $DocCommentId]);
            if(is_null($evidenciaObj)){
                throw new \Exception("Evidencia no encontrada", 404);
            }

            $evidenciaObj->number_times_review +=1;
            $evidenciaObj->reviewer_user_id = $user->id;
            $evidenciaObj->status = $status;
            $evidenciaObj->time_review = (new \DateTime())->format('Y-m-d H:i:s');
            $evidenciaObj->save();
            // TODO: Falta guardar el comentario


            return response()->json([
                'message' => 'Evidencia actualizada',
            ], 200);

        } catch (\Exception $ex) {
            \Log::error($ex->getMessage());
            $code = (int) $ex->getCode();
            return response()->json([
                'code' => (int) $ex->getCode(),
                'message' => $ex->getMessage(),
        ], $code);
        }
    }
}
