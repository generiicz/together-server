<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;

class RegisterController extends Controller
{

    /**
     * @SWG\Definition(
     *            definition="UserAuth",
     *            required={"email", "password"},
     * 			@SWG\Property(property="email", type="string"),
     * 			@SWG\Property(property="password", type="string"),
     *        )
     */

    /**
     * @SWG\Post(
     *      path="/auth/registration",
     *      operationId="registration",
     *      tags={"auth"},
     *      summary="User registration",
     *      description="Register user with token",
     *   @SWG\Parameter(
     *     name="user", in="body", required=true, description="Expertise item to create",
     *     @SWG\Schema(ref="#/definitions/UserAuth"),
     *   ),
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
    public function registration(Request $request)
    {
        $validator = $this->getValidationFactory()->make($request->all(), [
            'email' => 'required|email|max:255',
            'password' => 'required|max:30|min:6',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $user = User::query()->where('email', $request->input('email'))->first();
        if ($user) {
            return $this->validationError(['email' => 'Email already registered']);
        }

        $user = new User();
        $user->fill($request->only(['email', 'password']));
        $user->name = uniqid('User:');
        if (!$user->save()) {
            return $this->sendJsonErrors('User not save');
        }

        return $this->sendJson([
                'user' => $user,
                'token' => $user->createToken('auth' . $user->email)->accessToken]
        );
    }

    /**
     * @SWG\Definition(
     *            definition="AuthSoc",
     *            required={"type", "token"},
     * 			@SWG\Property(property="type", type="string"),
     * 			@SWG\Property(property="token", type="string"),
     *        )
     */

    /**
     * @SWG\Post(
     *      path="/auth/login",
     *      operationId="login",
     *      tags={"auth"},
     *      summary="User login",
     *      description="Login user by password",
     *   @SWG\Parameter(
     *     name="user", in="body", required=true, description="Expertise item to create",
     *     @SWG\Schema(ref="#/definitions/UserAuth"),
     *   ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *       @SWG\Response(response=400, description="Bad request"),
     *     )
     */

    /**
     * @SWG\Post(
     *      path="/auth/social",
     *      operationId="Social login",
     *      tags={"auth"},
     *      summary="Social User login",
     *      description="Social Login user by password",
     *  @SWG\Parameter(
     *     name="user", in="body", required=true, description="Expertise item to create",
     *     @SWG\Schema(ref="#/definitions/AuthSoc"),
     *   ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *       @SWG\Response(response=400, description="Bad request"),
     *     )
     */
}
