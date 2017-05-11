<?php

namespace App\Http\Controllers\Api;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;

class PostController extends Controller
{

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
    public function index(Request $request)
    {
        $postCollection = Category::query()->get();

        return $this->sendJson([
            'posts' => $postCollection
        ]);
    }

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
    public function one(Request $request, $id)
    {
        $post = Article::query()->find($id);
        if (!$post) {
            return $this->sendJsonErrors('Invalid post id', 404);
        }

        return $this->sendJson([
            'post' => $post
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
            'title' => 'required|string|max:25|min:3',
            'info'  => 'string|max:25|min:3',
            'date_from'  => 'date|required',
            'date_to'  => 'date|required',
            'time_from'  => 'time|required',
            'time_to'  => 'time|required',
            'address'  => 'string|required',
            'category_id'  => 'required',
            'is_private'  => 'bool',
            'number_extra_tickets'  => 'required',
            'lat'  => 'required',
            'lng'  => 'required',
            'cover' => 'required_without:name|file|mimes:jpeg,bmp,png,jpg',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        /** @var Article $model */
        $model = new Article();
        $model->fill($request->all());
        if ($request->hasFile('cover')) {
            $model->saveCoverByFile($request->file('cover'));
        }

        if (!$model->save()) {
            return $this->sendJsonErrors('Post not save. DB error');
        }

        return $this->sendJson([
            'post' => $model
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

    public function create(Request $request, $id)
    {
        $post = Article::query()->find($id);
        if (!$post) {
            return $this->sendJsonErrors('Invalid post id', 404);
        }

        $validator = $this->getValidationFactory()->make($request->all(), [
            'title' => 'required|string|max:25|min:3',
            'info'  => 'string|max:25|min:3',
            'date_from'  => 'date|required',
            'date_to'  => 'date|required',
            'time_from'  => 'time|required',
            'time_to'  => 'time|required',
            'address'  => 'string|required',
            'category_id'  => 'required',
            'is_private'  => 'bool',
            'number_extra_tickets'  => 'required',
            'lat'  => 'required',
            'lng'  => 'required',
            'cover' => 'required_without:name|file|mimes:jpeg,bmp,png,jpg',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        /** @var Article $model */
        $model = new Article();
        $model->fill($request->all());
        if ($request->hasFile('cover')) {
            $model->saveCoverByFile($request->file('cover'));
        }

        if (!$model->save()) {
            return $this->sendJsonErrors('Post not save. DB error');
        }

        return $this->sendJson([
            'post' => $model
        ]);
    }



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
    public function categories(Request $request)
    {
        $collection = Category::query()->get();

        return $this->sendJson([
            'categories' => $collection
        ]);
    }
}
