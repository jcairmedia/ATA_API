<?php

namespace App\Http\Controllers\API;

use App\BlogEntry;
use App\Http\Controllers\Controller;
use App\Http\Requests\Entry\AddEntryRequest;
use App\Http\Requests\Entry\DeleteEntryRequest;
use App\Http\Requests\Entry\FilterGetEntriesRequest;
use App\Http\Requests\Entry\UpdateEntryRequest;
use App\Http\Requests\Entry\UpdateFileEntryRequest;
use App\Main\BlogEntry\Domain\AddEntryDomain;
use App\Main\BlogEntry\Domain\DeleteEntryDomain;
use App\Main\BlogEntry\Domain\PaginateEntriesDomain;
use App\Main\BlogEntry\Domain\UpdateEntryDomain;
use App\Utils\FileUtils;

class EntriesBlogController extends Controller
{
    /**
     * @OA\Get(
     *  path="/api/blog/entries",
     *  summary="Consulta de las entradas del blog",
     *  security={{"bearer_token":{}}},
     *  @OA\Parameter(in="query",
     *       required=false,
     *       description="Número de página a mostrar, comienza desde 0",
     *       name="index",
     *       required=false,
     *       @OA\Schema(
     *          type="number",
     *       ),
     *       example="0"),
     *  @OA\Parameter(in="query",
     *       required=false,
     *       description="Número de registros por página",
     *       name="byPage",
     *       required=false,
     *       @OA\Schema(
     *          type="number",
     *       ),
     *       example="100"),
     *  @OA\Parameter(in="query",
     *       required=false,
     *       description="Identificador único de la categoria",
     *       name="categoryId",
     *       required=false,
     *       @OA\Schema(
     *          type="number",
     *       ),
     *       example="1"),
     *  @OA\Response(
     *    response=200,
     *    description="Ok",
     *    @OA\JsonContent(
     *      @OA\Property(
     *        property="complete",
     *        type="boolean",
     *        example="true",
     *      ),
     *      @OA\Property(
     *        property="total",
     *        type="number",
     *        example="9",
     *      ),
     *      @OA\Property(
     *        property="index",
     *        type="number",
     *        example="0",
     *      ),
     *      @OA\Property(
     *        property="rows",
     *        type="array",
     *        collectionFormat="multi",
     *        @OA\Items(
     *            type="object",
     *            @OA\Property(property="id", type="number", example="1"),
     *            @OA\Property(property="external_category_id", type="number", example="1"),
     *            @OA\Property(property="title", type="string", example="titulo de prueba"),
     *            @OA\Property(property="description", type="string", example="descripción de prueba"),
     *            @OA\Property(property="body", type="string", example="Cuerpo de la entrada"),
     *            @OA\Property(property="url_img_main", type="string", example="src_entries_blogs/2021-04-19_210628607e3724cb3d7597285580.png"),
     *            @OA\Property(property="name_img_main", type="string", example="Espolon_Blanco_750mL.png"),
     *            @OA\Property(property="status", type="string", example="PUBLISHED"),
     *            @OA\Property(property="category", type="number", example="Mis derechos en el trasnporte publico")
     *        )
     *      )
     *    )
     *  )
     * )
     */
    public function index(FilterGetEntriesRequest $request)
    {
        $byPage = $request->input('byPage') ?? 100;
        $index = $request->input('index') ?? 0;
        $categoryId = $request->input('categoryId') ?? 0;
        $config = [];
        if ($categoryId != 0) {
            $config = [['blog_entries.external_category_id' => $categoryId]];
        }
        $status = $request->input('status') ?? '';
        $objsEntries = (new PaginateEntriesDomain())($status, $index, $byPage, $config);

        return response()->json($objsEntries, 200);
    }

    public function deleteEntry(DeleteEntryRequest $request)
    {
        try {
            $entryId = $request->input('id');
            $response = (new DeleteEntryDomain())($entryId);
            if ($response) {
                return response()->json(['message' => 'Entrada eliminada'], 200);
            }

            return response()->json(['message' => 'Sucedio un error al tratar de eliminar'], 200);
        } catch (\Exception $ex) {
            $code = (int) $ex->getCode();
            if (!(($code >= 400 && $code <= 422) || ($code >= 500 && $code <= 503))) {
                $code = 500;
            }

            return response()->json([
                'code' => (int) $code,
                'message' => $ex->getMessage(),
            ], $code);
        }
    }

    public function updateEntry($blogEntryId, UpdateEntryRequest $request)
    {
        try {
            $data = $request->all();
            \Log::error('all'.print_r($data, 1));
            $isUpdate = (new UpdateEntryDomain())((int) $blogEntryId, $data);

            return response()->json(['message' => 'Entrada actualizada'], 200);
        } catch (\Exception $ex) {
            $code = (int) $ex->getCode();
            if (!(($code >= 400 && $code <= 422) || ($code >= 500 && $code <= 503))) {
                $code = 500;
            }

            return response()->json([
                'code' => (int) $code,
                'message' => $ex->getMessage(),
            ], $code);
        }
    }

    public function updateFile($blogEntryId, UpdateFileEntryRequest $request)
    {
        try {
            BlogEntry::findOrFail($blogEntryId);
            $uuid = preg_replace('/[^A-Za-z0-9\-\_]/', '', uniqid(date('Y-m-d_His'), true));

            $fileClass = new FileUtils($request);
            $propertiesField = $fileClass->read('file');
            $pathNameFile = $fileClass->save('src_entries_blogs', $uuid.'.'.$propertiesField[1]);

            $urldoc = $pathNameFile;
            $namedoc = $propertiesField[0];
            $data = [
                'url_img_main' => $urldoc,
                'name_img_main' => $namedoc,
            ];
            //Update record BD
            $isUpdate = (new UpdateEntryDomain())((int) $blogEntryId, $data);
            if ($isUpdate) {
                return response()->json([
                    'message' => 'Imagen principal actualizada',
                ], 200);
            }

            return response()->json([
                'message' => 'Error al tratar de actualizar la imagen',
            ], 404);
        } catch (\Exception $ex) {
            $code = (int) $ex->getCode();
            if (!(($code >= 400 && $code <= 422) || ($code >= 500 && $code <= 503))) {
                $code = 500;
            }

            return response()->json([
                'code' => (int) $code,
                'message' => $ex->getMessage(),
            ], $code);
        }
    }

    public function addEntry(AddEntryRequest $request)
    {
        // code...
        try {
            $title = $request->input('title');
            $description = $request->input('description');
            $body = $request->input('body');
            $status = $request->input('status');
            $categoryId = $request->input('categoryId');

            $uuid = preg_replace('/[^A-Za-z0-9\-\_]/', '', uniqid(date('Y-m-d_His'), true));

            $fileClass = new FileUtils($request);
            $propertiesField = $fileClass->read('file');
            $pathNameFile = $fileClass->save('src_entries_blogs', $uuid.'.'.$propertiesField[1]);

            $urldoc = $pathNameFile;
            $namedoc = $propertiesField[0];
            // TODO: guardar archivo
            $responseDomain = ((new AddEntryDomain())(new BlogEntry([
                'external_category_id' => $categoryId,
                'title' => $title,
                'description' => $description,
                'body' => $body,
                'url_img_main' => $urldoc,
                'name_img_main' => $namedoc,
                'status' => $status,
            ])));

            return response()->json(['message' => 'Entrada creada'], 201);
        } catch (\Exception $ex) {
            $code = (int) $ex->getCode();
            if (!(($code >= 400 && $code <= 422) || ($code >= 500 && $code <= 503))) {
                $code = 500;
            }

            return response()->json([
                'code' => (int) $code,
                'message' => $ex->getMessage(),
            ], $code);
        }
    }
}
