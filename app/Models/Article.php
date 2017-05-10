<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $fillable = array(
        'title',
        'info',
        'cover',
        'date_from',
        'date_to',
        'time_from',
        'time_to',
        'category_id',
        'is_private',
        'number_extra_tickets',
        'address',
        'lat',
        'lng',
        'status',
    );

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * @return User
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getCategory()
    {
        if (!isset($this->category->id) || $this->category->status != self::STATUS_ACTIVE) {
            $this->category = (new Category())->setDefault();
        }
        return $this->category;
    }

    public function getUser()
    {
        return (isset($this->user)) ? $this->user : App::make('\App\Modules\VergoBase\Database\Models\User')->setDefault();
    }
}
