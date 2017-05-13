<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class UserRelationship
 * @package App\Models
 * @property User $user
 * @property User $friend
 * @property \DateTime $deleted_at
 */
class UserRelationship extends Model
{
    protected $table = "user_relationship";

    protected $fillable = [
        'user_id',
        'friend_id',
        'deleted_at'
    ];

    /**
     * Get the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the friend
     */
    public function friend()
    {
        return $this->belongsTo(User::class, 'friend_id');
    }
}
