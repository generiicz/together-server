<?php

namespace App\Models;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Intervention\Image\ImageManager as Image;

class User extends Authenticatable
{
    const ACTIVE = 1;
    const BAN = 0;

    const DEF_AGE = 18;

    const ALIEN = 'alien';
    const MALE = 'male';
    const FEMALE = 'female';

    use Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'updated_at', 'status'
    ];

    public function saveCoverByUrl($url)
    {
        $filename = basename($url);
        $this->cover = 'app/user_avatars/' . $filename;

        $manager = new Image();
        $manager->make($url)->save(storage_path($this->cover));
    }
}
