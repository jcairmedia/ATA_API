<?php

namespace App\Http\Controllers;

class LoginController extends Controller
{
    /**
     * @OA\Post(
     *  path="/oauth/token",
     *  summary="Log In",
     *  @OA\RequestBody(
     *   required=true ,
     *   description="Log In con el método tradicional",
     *    @OA\MediaType(mediaType="application/x-www-form-urlencoded",
     *      @OA\Schema(
     *          required={"grant_type", "client_id", "client_secret"},
     *          @OA\Property(property="grant_type", type="string", example="password"),
     *          @OA\Property(property="client_id", type="string", example="4"),
     *          @OA\Property(property="client_secret", type="string", example="Kagy4h0z9QsXJBNRgvL5VXwbeUmwEeTdbAYwt4lA"),
     *          @OA\Property(property="username", type="string", example="erika@airmedia.com.mx", description="Es requerido cuando grant_type=password" ),
     *          @OA\Property(property="password", type="string", example="12345678", description="Es requerido cuando grant_type=password"),
     *          @OA\Property(property="scope", type="string", example="2", description="Es requerido cuando grant_type=password"),
     *          @OA\Property(property="token", type="string", example="2", description="Este valor corresponde al access_token que entrega Facebook. Es requerido cuando grant_type=facebook"),
     *
     *     )
     *   )
     *  ),
     *  @OA\Response(
     *      response=401,
     *      description="Unauthorized",
     *      @OA\JsonContent(
     *          @OA\Property(
     *              property="message",
     *              type="string",
     *              example="Unauthenticated"
     *          )
     *      )
     *
     *  ),
     *  @OA\Response(
     *    response=400,
     *    description="Bad Request",
     *    @OA\JsonContent(
     *      @OA\Property(
     *        property="error",
     *        type="string",
     *        example="invalid_grant"
     *      ),
     *      @OA\Property(
     *        property="error_description",
     *        type="string",
     *        example="The provided authorization grant (e.g., authorization code, resource owner credentials) or refresh token is invalid, expired, revoked, does not match the redirection URI used in the authorization request, or was issued to another client."
     *      ),
     *      @OA\Property(
     *        property="hint",
     *        type="string",
     *        example=""
     *      ),
     *      @OA\Property(
     *        property="message",
     *        type="string",
     *        example="The provided authorization grant (e.g., authorization code, resource owner credentials) or refresh token is invalid, expired, revoked, does not match the redirection URI used in the authorization request, or was issued to another client."
     *      ),
     *    )),
     * *  @OA\Response(
     *    response=200,
     *    description="OK",
     *    @OA\JsonContent(
     *      @OA\Property(
     *        property="token_type",
     *        type="string",
     *        example="Bearer"
     *      ),
     *      @OA\Property(
     *        property="expires_in",
     *        type="string",
     *        example="1296000"
     *      ),
     *      @OA\Property(
     *        property="access_token",
     *        type="string",
     *        example="eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9...."
     *      ),
     *      @OA\Property(
     *        property="refresh_token",
     *        type="string",
     *        example="def5020073e0ffbc1c1d8888481987032b6ef29733bb29f396df4680f1fa0628dd81f2cb5154d72c933dd898f0422bd72700281a96bc509e08ba6d9851493f312d86228ed1140666e0bbd71e4253ac55b7cf361cf5dde350f19bd30db8da277b5882f5873c3b9fed61dd4d9c81a8ccaf313febeda3041c1776493127c9aab5534aeb46df27020e4d9e8013cb24bf7669f99f0da42ae1be0a21a4b684461a8d0948c625934edbdd2ac35f33633f4f25d4eb31262c61fe66495f82cadd1b0ccafba105b09b54278e1e3c3600bd80b1e3195c3f2fcb83158b61d20f811c2a263ede290697bec8a4ff62ca7d537993af6776b0fc57f11e077f3efaa4a90fb33fcd27cea40e690ccdff1a6116bbc34a25470043f5ed0ced0c6691434b1892c31e6805c0603fd5892f6efc3c9b6846ec8b4e08fca8067ac20c54f1da7bd3f7dad2176423143ddd1b7546aca879b9fd18bae67c4d22ae5cba3276b7f3a21813127428e596f157c8"
     *      ),
     *    )),
     * )
     */
    public function index()
    {
        // code...
    }
}
