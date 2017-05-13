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
     *            definition="UserInfo",
     * 			@SWG\Property(property="id", type="integer"),
     * 			@SWG\Property(property="name", type="string"),
     * 			@SWG\Property(property="sex", type="string"),
     * 			@SWG\Property(property="age", type="integer"),
     * 			@SWG\Property(property="cover", type="string"),
     *        )
     */
    /**
     * @SWG\Definition(
     *            definition="UserList",
     * 			@SWG\Property(property="data", type="array", items=@SWG\Schema(ref="#/definitions/UserInfo"),),
     *        )
     */

    /**
     * @SWG\Definition(
     *            definition="filter",
     * 			@SWG\Property(property="age", type="integer"),
     * 			@SWG\Property(property="sex", type="string"),
     *        )
     */

    /**
     * @SWG\Post(
     *      path="/filter",
     *      operationId="filterUser",
     *      tags={"user"},
     *      summary="Search users by age or/and sex",
     *      description="Search users",
     *      security={{"X-Api-Token":{}}},
     *      @SWG\Parameter(
     *          name="filter", in="body", required=false, description="User Post Data",
     *          @SWG\Schema(ref="#/definitions/filter"),
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          @SWG\Schema(ref="#/definitions/UserList"),
     *          description="successful operation"
     *       ),
     *       @SWG\Response(response=400, description="Bad request"),
     *     )
     */
    public function filterAction(Request $request)
    {
        /**
         * Filters list
         * May use another filters.. for example may use filter by name
         * in task TOG-3
         * Tito, what you think about it?
         */
        $validator = $this->getValidationFactory()->make($request->all(), [
            'sex' => 'string|in:' . implode(",", User::sexList()),
            'age' => 'integer',
        ]);
        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }
        /**
         * Using query builder and scopes
         * To search users
         */
        $builder = new User();

        if ($request->get("sex")) {
            $builder = $builder->ofSex(User::MALE);
        }
        if ($request->get("age")) {
            $builder = $builder->ofAge("<", 19);
        }

        $collection = $builder->get();
        return $this->sendJson($collection);
    }
}
