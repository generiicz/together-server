<?php

namespace App\Http\Controllers\Api;

use App\Models\FbUser;
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
     * @SWG\Definition(
     *            definition="AuthSoc",
     *            required={"type", "access_token"},
     * 			@SWG\Property(property="type", type="string", enum={"fb", "tw"}),
     * 			@SWG\Property(property="access_token", type="string"),
     *        )
     */

    /**
     * @SWG\Post(
     *      path="/auth/soc",
     *      operationId="Social Twitter/Facebook auth",
     *      tags={"auth"},
     *      summary="Social User Twitter/Facebook auth",
     *      description="Social user Twitter/Facebook auth",
     *      @SWG\Parameter(
     *          name="user", in="body", required=true, description="Post Data",
     *          @SWG\Schema(ref="#/definitions/AuthSoc"),
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *       @SWG\Response(response=400, description="Bad request"),
     *     )
     */
    public function socAuth(Request $request)
    {
        $validator = $this->getValidationFactory()->make($request->all(), [
            'access_token' => 'required|string',
            'type' => 'required|size:2',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $input = $request->all();
        if (!in_array($input['type'], ['fb', 'tw'])) {
            return $this->sendJsonErrors('Invalid Type', 403);
        }
        if ($input['type'] == 'tw') {
            return $this->twAuth($input['access_token']);
        }

        return $this->fbAuth($input['access_token']);
    }

    public function twAuth($accessToken)
    {
        Log::info('Auth with tw access_token:' . $accessToken);
        $connection = new TwitterOAuth(env('TW_CLIENT_ID'), env('TW_CLIENT_SECRET'), $accessToken, env('TW_YOUR_SECRET'));
        $content = $connection->get("account/verify_credentials");

        if (!isset($content->name, $content->id)) {
            return $this->sendJsonErrors('Invalid Soc Token', 403);
        }

        $socUser = TwUser::query()->find($content->id);
        if (isset($socUser->user)) {
            return $this->sendJson([
                    'user' => $socUser->user,
                    'token' => $socUser->user->createToken('auth' . $socUser->user->email)->accessToken]
            );
        }

        $user = new User();
        $user->name = $content->name;
        $user->email = $content->id . '@tw.com';
        $user->password = '';
        if (isset($content->profile_image_url) && $content->profile_image_url) {
            $user->saveCoverByUrl($content->profile_image_url);
        }
        if (!$user->save()) {
            return $this->sendJsonErrors('User not save');
        }

        $socUser = new TwUser();
        $socUser->fill(['id' => (int) $content->id, 'token' => $accessToken]);
        $socUser->user_id = $user->id;
        if (!$socUser->save()) {
            return $this->sendJsonErrors('Account not save. DB error');
        }

        return $this->sendJson([
                'user' => $user,
                'token' => $user->createToken('auth' . $user->email)->accessToken]
        );
    }


    public function fbAuth($accessToken)
    {

        $driver = Socialite::driver('facebook');
        $driver = $driver->fields(['name', 'email', 'gender', 'verified', 'link', 'age_range']);
        try {
            $content = $driver->userFromToken($accessToken);
        } catch (\Exception $exception) {
            return $this->sendJsonErrors('Invalid Soc Token. Fb api error', 500);
        }

        if (!isset($content->name, $content->id)) {
            return $this->sendJsonErrors('Invalid Soc Token', 403);
        }

        $socUser = FbUser::query()->find((int) $content->id);
        if (isset($socUser->user)) {
            return $this->sendJson([
                    'user' => $socUser->user,
                    'token' => $socUser->user->createToken('auth' . $socUser->user->email)->accessToken]
            );
        }


        $user = new User();
        $user->name = $content->name;
        $user->email = (isset($content->user['email']) && $content->user['email']) ? $content->user['email'] : $content->id . '@fb.com';
        $user->sex = (isset($content->user['gender']) && $content->user['gender']) ? $content->user['gender'] : User::ALIEN;
        $user->age = (isset($content->user['age_range']['min']) && $content->user['age_range']['min']) ? $content->user['age_range']['min'] : User::DEF_AGE;
        $user->password = '';
        $avatar = isset($content->avatar_original) ? $content->avatar_original : $content->avatar;
        if ($avatar) {
            $user->saveCoverByUrl($avatar);
        }
        if (!$user->save()) {
            return $this->sendJsonErrors('User not save');
        }

        $socUser = new FbUser();
        $socUser->fill(['id' => $content->id, 'token' => $accessToken]);
        $socUser->user_id = $user->id;
        if (!$socUser->save()) {
            return $this->sendJsonErrors('Account not save. DB error');
        }

        return $this->sendJson([
                'user' => $user,
                'token' => $user->createToken('auth' . $user->email)->accessToken]
        );
    }
}
