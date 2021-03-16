<?php

namespace App\Main\FederalEntities\Domain;

use App\Federalentitie;

class FederalEntitiesDomain
{
    /**
     * @OA\Get(
     *  path="/api/federalentities",
     *  summary="Consulta de entidades federativas",
     *  @OA\Response(
     *    response=200,
     *    description="Ok",
     *    @OA\JsonContent(
     *      @OA\Property(
     *        property="data",
     *        type="array",
     *        collectionFormat="multi",
     *        @OA\Items(
     *            type="object",
     *            @OA\Property(property="id", type="number", example="1"),
     *            @OA\Property(property="idfe", type="number", example="1"),
     *            @OA\Property(property="name", type="string", example="Federal"),
     *        )
     *      ),
     *    )
     *  )
     * )
     */
    public function __invoke()
    {
        try {
            return Federalentitie::all();
        } catch (\Exception $ex) {
            \Log::error($ex->getMessage().'('.$ex->getCode().')');
            throw new \Exception($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}
