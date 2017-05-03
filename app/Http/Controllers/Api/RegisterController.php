<?php

namespace App\Http\Controllers\Api;

use App\Models\TwUser;
use Illuminate\Support\Facades\Log;
use Socialite;
use App\Models\User;
use Illuminate\Http\Request;
use Abraham\TwitterOAuth\TwitterOAuth;

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
     *     name="user", in="body", required=true, description="Post Data",
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
     * @SWG\Post(
     *      path="/auth/login",
     *      operationId="login",
     *      tags={"auth"},
     *      summary="User login",
     *      description="Login user by password",
     *   @SWG\Parameter(
     *     name="user", in="body", required=true, description="Post Data",
     *     @SWG\Schema(ref="#/definitions/UserAuth"),
     *   ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *       @SWG\Response(response=400, description="Bad request"),
     *     )
     */
    public function login(Request $request)
    {
        $validator = $this->getValidationFactory()->make($request->all(), [
            'email' => 'required|email|max:255',
            'password' => 'required|max:30|min:6',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $user = User::query()->where('email', $request->input('email'))->first();

        if (!$user) {
            return $this->sendJsonErrors('User not found', 404);
        }

        if ($user->password != $request->input('password')) {
            return $this->validationError('Password is invalid', 403);
        }

        return $this->sendJson([
                'user' => $user,
                'token' => $user->createToken('auth' . $user->email)->accessToken]
        );
    }


    /**
     *       @SWG\Definition(
     *            definition="AuthTw",
     *            required={"access_token", "access_token_secret"},
     * 			@SWG\Property(property="access_token", type="string"),
     * 			@SWG\Property(property="access_token_secret", type="string"),
     *        )
     */

    /**
     * @SWG\Post(
     *      path="/auth/tw",
     *      operationId="Social Twitter auth",
     *      tags={"auth"},
     *      summary="Social User Twitter auth",
     *      description="Social user Twitter auth",
     *  @SWG\Parameter(
     *     name="user", in="body", required=true, description="Post Data",
     *     @SWG\Schema(ref="#/definitions/AuthTw"),
     *   ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *       @SWG\Response(response=400, description="Bad request"),
     *     )
     */
    public function twAuth(Request $request)
    {
        $validator = $this->getValidationFactory()->make($request->all(), [
            'access_token' => 'required',
            'access_token_secret' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $accessToken = $request->input('access_token');
        $accessTokenSecret = $request->input('access_token_secret');
        Log::info('Auth with tw access_token:' . $accessToken . ' secret: ' . $accessTokenSecret);
        $connection = new TwitterOAuth(env('TW_CLIENT_ID'), env('TW_CLIENT_SECRET'), $accessToken, $accessTokenSecret);
        $content = $connection->get("account/verify_credentials");

        if(!isset($content->name, $content->id)){
            return $this->sendJsonErrors('Invalid Soc Token', 403);
        }

        $socUser = TwUser::query()->find($content->id);
        if(isset($socUser->user)) {
            return $this->sendJson([
                'user' => $socUser->user,
                'token' => $socUser->user->createToken('auth' . $socUser->user->email)->accessToken]
            );
        }

        $user = new User();
        $user->name = $content->name;
        $user->email = $content->id . '@twitter.com';
        $user->password = '';
        if(isset($content->profile_image_url) && $content->profile_image_url) {
            $user->saveCoverByUrl($content->profile_image_url);
        }
        if (!$user->save()) {
            return $this->sendJsonErrors('User not save');
        }

        $socUser = new TwUser();
        $socUser->fill(['id' => $content->id, 'token' => $accessTokenSecret]);
        $socUser->user_id = $user->id;
        if(!$socUser->save()) {
            return $this->sendJsonErrors('Account not save. DB error');
        }


        return $this->sendJson([
                'user' => $user,
                'token' => $user->createToken('auth' . $user->email)->accessToken]
        );
    }

    /**
     * @SWG\Post(
     *      path="/auth/fb",
     *      operationId="Social Facebook auth",
     *      tags={"auth"},
     *      summary="Social User Facebook auth",
     *      description="Social user Facebook auth",
     *  @SWG\Parameter(
     *     name="user", in="body", required=true, description="Post Data",
     *     @SWG\Schema(
     *          @SWG\Property(property="access_token", type="string")
     *      ),
     *   ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *       @SWG\Response(response=400, description="Bad request"),
     *     )
     */
    public function fbAuth(Request $request)
    {
        $validator = $this->getValidationFactory()->make($request->all(), [
            'type' => 'required|size:2|string',
            'token' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        if (!in_array($request->input('type'), ['fb', 'tw'])) {
            return $this->sendJsonErrors('Invalid Type', 403);
        }
        $connection = new TwitterOAuth(env('TW_CLIENT_ID'), env('TW_CLIENT_SECRET'), '94858324-JzVVc6KYnFPwrnGK5LuN6B4O0qEzu11uBQquCByVd', 'yKrwj2SpaLHrMiEKhOKADQiqO2yDCeRtyZQxAjWlgUdXb');
        $content = $connection->get("account/verify_credentials");
        die(var_dump( $content,__METHOD__, __FILE__, __DIR__));
        try{
            $user = Socialite::driver('twitter')->userFromToken($request->input('token'));
            die(var_dump( $user,__METHOD__, __FILE__, __DIR__));
        } catch (\Exception $exception){
            die(var_dump( $exception,__METHOD__, __FILE__, __DIR__));
            return $this->sendJsonErrors('Invalid Soc Token', 403);
        }

        die(var_dump( $user,__METHOD__, __FILE__, __DIR__));

        if (!$user) {
            return $this->sendJsonErrors('User not found', 404);
        }
        if ($user->password != $request->input('password')) {
            return $this->validationError('Password is invalid', 403);
        }

        return $this->sendJson([
                'user' => $user,
                'token' => $user->createToken('auth' . $user->email)->accessToken]
        );
    }
}
