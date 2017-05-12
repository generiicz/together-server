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
     *      @SWG\Parameter(
     *          name="userUpdate", in="body", required=true, description="User Post Data",
     *          @SWG\Schema(ref="#/definitions/UserUpdate"),
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *       @SWG\Response(response=400, description="Bad request"),
     *     )
     *
     */

    public function update(Request $request)
    {
        $validator = $this->getValidationFactory()->make($request->all(), [
            'name' => 'required_without:cover|string|max:25|min:3',
            'cover' => 'required_without:name|file|mimes:jpeg,bmp,png,jpg',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        /** @var User $user */
        $user = $request->user();
        if ($request->hasFile('cover')) {
            $user->saveCoverByFile($request->file('cover'));
        }

        if ($request->input('name')) {
            $user->name = $request->input('name');
        }

        if (!$user->save()) {
            return $this->sendJsonErrors('Account not save. DB error');
        }

        return $this->sendJson([
            'user' => $user
        ]);
    }

    /**
     * @SWG\Definition(
     *            definition="UserToFind",
     * 			@SWG\Property(property="name", type="string"),
     *        )
     */
    /**
     * @SWG\Definition(
     *            definition="UserInfo",
     * 			@SWG\Property(property="id", type="integer"),
     * 			@SWG\Property(property="name", type="string"),
     * 			@SWG\Property(property="cover", type="string"),
     *        )
     */
    /**
     * @SWG\Definition(
     *            definition="UserList",
     * 			@SWG\Property(property="list", type="array", items=@SWG\Schema(ref="#/definitions/UserInfo"),),
     *        )
     */

    /**
     * @SWG\Post(
     *      path="/find",
     *      operationId="findByName",
     *      tags={"user"},
     *      summary="Find User",
     *      description="Find User by Name",
     *      security={{"X-Api-Token":{}}},
     *      @SWG\Parameter(
     *          name="userName", in="body", required=true, description="User Post Data",
     *          @SWG\Schema(ref="#/definitions/UserToFind"),
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          @SWG\Schema(ref="#/definitions/UserList"),
     *          description="successful operation"
     *       ),
     *       @SWG\Response(response=400, description="Bad request"),
     *     )
     *
     */
    public function findByNameAction(Request $request)
    {
        $validator = $this->getValidationFactory()->make($request->all(), [
            'name' => 'required|string|max:25|min:3',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }
        $findCollection = User::query()
            ->where("name", "like", "%" . $request->get("name") . "%")
            ->get();
        $result = [];
        foreach ($findCollection as $user) {
            /**
             * Todo: Process information by user
             */
            $result[] = $user;
        }
        return $this->sendJson(["list" => $result]);
    }
}
