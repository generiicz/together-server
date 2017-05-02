<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{

    /**
     * @SWG\Get(
     *      path="/user",
     *      operationId="getUserInfo",
     *      tags={"user"},
     *      summary="User information",
     *      description="Get user",
     *      security={{"X-Api-Token":{}}},
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *       @SWG\Response(response=400, description="Bad request"),
     *     )
     *
     * Returns Auth User
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        return $this->sendJson([
            'user' => $request->user()
        ]);
    }

    /**
     * @SWG\Definition(
     *            definition="UserUpdate",
     * 			@SWG\Property(property="name", type="string"),
     * 			@SWG\Property(property="cover", type="file"),
     *        )
     */

    /**
     * @SWG\Post(
     *      path="/user",
     *      operationId="updateUserInfo",
     *      tags={"user"},
     *      summary="Update User information",
     *      description="Update user",
     *      security={{"X-Api-Token":{}}},
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *       @SWG\Response(response=400, description="Bad request"),
     *     )
     *
     */
}
