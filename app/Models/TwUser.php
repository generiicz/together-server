<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TwUser extends Model
{
    public $timestamps = false;

    public $incrementing = false;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'token', 'id'
    ];

    protected $fillable = [
        'id', 'token',
    ];

    /**
     * Get the user that the token belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
