<?php
/**
 * Created by PhpStorm.
 * User: selmarinel
 * Date: 12.05.17
 * Time: 8:41
 */

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\UserRelationship;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class FriendController extends Controller
{

    /**
     * @SWG\Definition(
     *            definition="UserFriend",
     * 			@SWG\Property(property="friend_id", type="integer"),
     *        )
     */

    /**
     * @SWG\Post(
     *      path="/friend/add",
     *      operationId="addToFriend",
     *      tags={"user","friend"},
     *      summary="Add Friend",
     *      description="Add User by id to friend",
     *      security={{"X-Api-Token":{}}},
     *      @SWG\Parameter(
     *          name="userFriend", in="body", required=true, description="User Friend Post Data",
     *          @SWG\Schema(ref="#/definitions/UserFriend"),
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *       @SWG\Response(response=400, description="Bad request"),
     *     )
     *
     * Returns Result of Action "Adding to friend"
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addFriendAction(Request $request)
    {
        $validator = $this->getValidationFactory()->make($request->all(), [
            'friend_id' => 'required|numeric|exists:users,id',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }
        /** @var User $user */
        $user = $request->user();
        /** @var UserRelationship $relationship */
        $relationship = UserRelationship::query()
            ->where('user_id', $user->id)
            ->where('friend_id', $request->get("friend_id"))
            ->first();
        /**
         * If exist relation and it was deleted
         * Remove deleted_at
         */
        if ($relationship) {
            if ($relationship->deleted_at) {
                $relationship->deleted_at = null;
            } else {
                /**
                 * Or id relation exist, but deleted_at is null
                 * It's seems that relation is active
                 */
                return $this->sendJsonErrors('User already added');
            }
        } else {
            /**
             * In the other hand
             * Create relationship
             */
            $relationship = new UserRelationship();
            $relationship->fill([
                "user_id" => $user->id,
                "friend_id" => $request->input('friend_id')
            ]);
        }
        if (!$relationship->save()) {
            return $this->sendJsonErrors('Relation not saved');
        }
        return $this->sendJson([
            'user' => $user
        ]);

    }

    /**
     * @SWG\Post(
     *      path="/friend/remove",
     *      operationId="removeFromFriend",
     *      tags={"user","friend"},
     *      summary="Remove Friend",
     *      description="Remove Friend by id",
     *      security={{"X-Api-Token":{}}},
     *      @SWG\Parameter(
     *          name="userFriend", in="body", required=true, description="User Friend Post Data",
     *          @SWG\Schema(ref="#/definitions/UserFriend"),
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *       @SWG\Response(response=400, description="Bad request"),
     *     )
     *
     * Returns Result of Action "Removing to friend"
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeFriendAction(Request $request)
    {
        $validator = $this->getValidationFactory()->make($request->all(), [
            'friend_id' => 'required|numeric|exists:users,id',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        /** @var User $user */
        $user = $request->user();
        /** @var UserRelationship $relationship */
        $relationship = UserRelationship::query()
            ->where('user_id', $user->id)
            ->where('friend_id', $request->get("friend_id"))
            ->first();
        /**
         * If not exist relation
         */
        if (!$relationship) {
            return $this->sendJsonErrors('Relationship is not exists');
        }
        /**
         * If exist relation
         * And deleted at is not null
         */
        if ($relationship->deleted_at) {
            return $this->sendJsonErrors('User already removed');
        }
        /**
         *
         */
        $relationship->deleted_at = new \DateTime("now");
        if (!$relationship->save()) {
            return $this->sendJsonErrors('Relation not saved');
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
     * 			@SWG\Property(property="cover", type="string"),
     *        )
     */

    /**
     * @SWG\Definition(
     *            definition="FriendsList",
     * 			@SWG\Property(property="data", type="array", items=@SWG\Schema(ref="#/definitions/UserInfo"),),
     *        )
     */

    /**
     * @SWG\Get(
     *      path="/friend/list",
     *      operationId="friendsList",
     *      tags={"user","friend"},
     *      summary="Show List",
     *      description="Show Friend list",
     *      security={{"X-Api-Token":{}}},
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(ref="#/definitions/FriendsList"),
     *       ),
     *       @SWG\Response(response=400, description="Bad request"),
     *     )
     *
     * Returns friend list
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function friendsListAction(Request $request)
    {
        /** @var User $user */
        $user = $request->user();

        $list = [];
        foreach ($user->relations as $user) {
            $list[] = $user->getBaseInfo();
        }
        return $this->sendJson($list);
    }
}