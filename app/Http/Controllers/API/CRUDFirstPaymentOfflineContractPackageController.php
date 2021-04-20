<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Main\Cases\Domain\CaseFindDomain;
use App\Main\Documents_Cases\Domain\FindDocumentCaseDomain;
use App\Main\Documents_Cases\Domain\InnerJoinDocCaseByFolioEvidenceDomain;
use App\Main\Documents_Cases\Domain\InnerJoinDocumentDomain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class CRUDFirstPaymentOfflineContractPackageController extends Controller
{
    /**
     * @OA\Get(
     *  path="/api/v2/evidences/byuser",
     *  tags={"App móvil"},
     *  summary="Obtener todas las evidencias del cliente (paginadas) ",
     *  security={{"bearer_token":{}}},
     *  @OA\Response(
     *    response=200,
     *    description="Ok",
     *    @OA\JsonContent(
     *      @OA\Property(
     *        property="complete",
     *        type="boolean",
     *        example="true"
     *      ),
     *      @OA\Property(
     *        property="total",
     *        type="number",
     *        example="4"
     *      ),
     *      @OA\Property(
     *        property="index",
     *        type="number",
     *        example="0"
     *      ),
     *      @OA\Property(
     *        property="row",
     *        type="array",
     *        collectionFormat="multi",
     *        @OA\Items(
     *            type="object",
     *            @OA\Property(property="id", type="number", example="1"),
     *            @OA\Property(property="reviewer_user_id", type="number", example="12", description="FK table users"),
     *            @OA\Property(property="case_id", type="number", example="12", description="FK table case"),
     *            @OA\Property(property="folio", type="number", example="606412154545484", description="Folio evidence"),
     *            @OA\Property(property="status", type="string", example="UPLOAD_PENDING", description="UPLOAD_PENDING: pendiente que el usuario suba su evidencia, IN_REVIEW_REVIEWER: en proceso de revisiòn, APPROVED: Aprobado, NO_APPROVED: no aprobado"),
     *            @OA\Property(property="comment", type="string", example="un comentario que tenga"),
     *            @OA\Property(property="time_review", type="string",format="date", example="2020-02-10 15:20", description="fecha en la cual se reviso la evidencia"),
     *            @OA\Property(property="number_times_review", type="number", example="1", description="Numero de veces revisada la evidencia"),
     *            @OA\Property(property="created_at", type="string", format="date", example="2020-02-10 15:20"),
     *            @OA\Property(property="updated_at", type="string", format="date", example="2020-02-10 15:20",),
     *            @OA\Property(property="package", type="string", example="Intermedio"),
     *            @OA\Property(property="service", type="string", example="Procedimiento y\/o juicio de divorcio voluntario"),
     *            @OA\Property(property="customer", type="string", example="Juan Perez Perez", description="Nombre del cliente"),
     *          )
     *      )
     *    )
     *  )
     * )
     */
    public function YoursEvidences(Request $request)
    {
        // Buscar sobre los casos
        try {
            $index = $request->input('index') ?? 0;
            $user = $request->user();
            $obj = (new InnerJoinDocumentDomain())(
                'IN_REVIEW_REVIEWER',
                $user->id,
                $index,
                $byPage = 100);

            return response()->json($obj, 200);
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

    public function allEvidences(Request $request)
    {
        try {
            $index = $request->input('index') ?? 0;
            $byPage = $request->input('byPage') ?? 100;

            $obj = (new InnerJoinDocumentDomain())('', 0, $index, $byPage);

            return response()->json($obj, 200);
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

    public function evidenceByFolio(Request $request)
    {
        try {
            $folio = $request->input('folio');
            $evidenceObj = (new InnerJoinDocCaseByFolioEvidenceDomain())($folio);

            return response()->json($evidenceObj->toArray(), 200);
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

    /**
     * Evaluate evidence.in dashboard.
     *
     * @return void
     */
    public function evaluate(Request $request)
    {
        try {
            $user = $request->user();
            $comment = $request->input('comment');
            $DocCommentId = $request->input('evidence_id');
            $status = $request->input('status');

            \Log::error('user: '.$user->id);
            \Log::error('doc: '.print_r($DocCommentId, 1));

            $evidenceObj = (new FindDocumentCaseDomain())(['id' => $DocCommentId]);
            if (is_null($evidenceObj)) {
                throw new \Exception('Evidencia no encontrada', 404);
            }
            if ($evidenceObj->status == $status) {
                throw new \Exception('La evidencia ya fue actualizada', 409);
            }
            if ($evidenceObj->status == 'APPROVED') {
                throw new \Exception('Evidencia aprobada', 409);
            }
            if ($evidenceObj->status == 'NO_APPROVED') {
                throw new \Exception('La evidencia no fue aprobada', 409);
            }
            if ($evidenceObj->status == 'UPLOAD_PENDING') {
                throw new \Exception('Pendiente que el usuario suba su evidencia', 409);
            }

            // validar el tiempo que le resta
            $lastChangeTime = $evidenceObj->time_review;
            if ($evidenceObj->number_times_review > 2 &&
            $evidenceObj->state == 'UPLOAD_PENDING') {
                throw new \Exception('Tiempo expirado', 409);
            }

            $evidenceObj->reviewer_user_id = $user->id;
            $evidenceObj->status = $status;
            $evidenceObj->comment = $comment;

            $evidenceObj->save();
            \Log::error('evidenceObj: '.print_r($evidenceObj, 1));

            // TODO: Falta guardar el comentario
            $caseObj = (new CaseFindDomain())(['id' => $evidenceObj->case_id]);
            \Log::error('caseFind: '.print_r($caseObj, 1));
            if ($status == 'APPROVED') {
                $caseObj->state_paid_opening = 1;
                $caseObj->save();
                //TODO: ENviar email de aprobación de caso
                // TODO: Guardar el pago en la tabla payments de casos
            }

            if ($status == 'NO_APPROVED') {
                $caseObj->state_paid_opening = 2;
                $caseObj->save();
                //TODO: ENviar email de no aprobación de caso
            }
            if ($status == 'IN_REVIEW_REVIEWER') {
                ++$evidenceObj->number_times_review;
                $evidenceObj->time_review = (new \DateTime())->format('Y-m-d H:i:s');
                $evidenceObj->save();
            }
            if ($status == 'UPLOAD_PENDING') {
                ++$evidenceObj->number_times_review;
                $evidenceObj->time_review = (new \DateTime())->format('Y-m-d H:i:s');
                $evidenceObj->save();
                //TODO: ENviar email de actualizacion de evidencia
            }

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

    /**
     * Get file evidence by name.
     *
     * @return void
     */

    /**
     * @OA\Get(
     *  path="/evidencefile",
     *  summary="Obtener o descargar el binario de un archivo",
     *  security={{"bearer_token":{}}},
     *  @OA\Parameter(in="query",
     *       required=false,
     *       description="Nombre del archivo que se guardo en BD",
     *       name="name",
     *       required=true,
     *       @OA\Schema(
     *          type="string",
     *       ),
     *       example="src_entries_blogs/2021-04-19_122338607dbc9aee3f9938024177.png"),
     *  @OA\Response(
     *    response=200,
     *    description="Binary stream",
     *
     *  )
     * )
     */
    public function getEvidence(Request $request)
    {
        $all = $request->all();
        $filePath = storage_path('app/'.$all['name']);
        if (!File::exists($filePath)) {
            return response('File not exists', 404);
        }
        $contents = File::get($filePath);

        return $contents;
    }
}
