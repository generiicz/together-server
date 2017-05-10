<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;

class PostController extends Controller
{

    /**
     * @SWG\Get(
     *      path="/post",
     *      operationId="getPostInfo",
     *      tags={"post"},
     *      summary="Post information",
     *      description="Get post",
     *      security={{"X-Api-Token":{}}},
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *       @SWG\Response(response=400, description="Bad request"),
     *     )
     *
     * Returns Post
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request, $id)
    {
        return $this->sendJson([
            'UserSeeder' => $request->user()
        ]);
    }

    /**
     * @SWG\Definition(
     *            definition="PostUpdate",
     * 			@SWG\Property(property="title", type="string"),
     * 			@SWG\Property(property="info", type="string"),
     * 			@SWG\Property(property="cover", type="file"),
     * 			@SWG\Property(property="date_from", type="date"),
     * 			@SWG\Property(property="date_to", type="date"),
     * 			@SWG\Property(property="time_from", type="time"),
     * 			@SWG\Property(property="time_to", type="time"),
     * 			@SWG\Property(property="address", type="string"),
     * 			@SWG\Property(property="category_id", type="number"),
     * 			@SWG\Property(property="is_private", type="boolean"),
     * 			@SWG\Property(property="number_extra_tickets", type="number"),
     * 			@SWG\Property(property="lat", type="string"),
     * 			@SWG\Property(property="lng", type="string"),
     *        )
     */

    /**
     * @SWG\Post(
     *      path="/post",
     *      operationId="post update",
     *      tags={"post"},
     *      summary="Update Post information",
     *      description="Update Post",
     *      security={{"X-Api-Token":{}}},
     *      @SWG\Parameter(
     *          name="postUpdate", in="body", required=true, description="Post Data",
     *          @SWG\Schema(ref="#/definitions/PostUpdate"),
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

        if($request->input('name')) {
            $user->name = $request->input('name');
        }

        if (!$user->save()) {
            return $this->sendJsonErrors('Account not save. DB error');
        }

        return $this->sendJson([
            'UserSeeder' => $user
        ]);
    }


    /**
     * @SWG\Post(
     *      path="/post/create",
     *      operationId="Post create",
     *      tags={"article"},
     *      summary="Create new Post",
     *      description="Create Post",
     *      security={{"X-Api-Token":{}}},
     *      @SWG\Parameter(
     *          name="postUpdate", in="body", required=true, description="Create Post Data",
     *          @SWG\Schema(ref="#/definitions/PostUpdate"),
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *       @SWG\Response(response=400, description="Bad request"),
     *     )
     *
     */



    /**
     * @SWG\Get(
     *      path="/post/all",
     *      operationId="Get All posts",
     *      tags={"post"},
     *      summary="Get All Posts",
     *      description="Get All Posts",
     *      security={{"X-Api-Token":{}}},
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *       @SWG\Response(response=400, description="Bad request"),
     *     )
     *
     */



    /**
     * @SWG\Get(
     *      path="/post/category",
     *      operationId="Get All posts category",
     *      tags={"post"},
     *      summary="Get All posts category",
     *      description="Get All posts category",
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
