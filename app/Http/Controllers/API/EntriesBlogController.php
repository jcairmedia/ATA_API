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
