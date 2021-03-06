<?php

namespace App\Models;

use Illuminate\Support\Facades\Storage;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Intervention\Image\ImageManager as Image;

class User extends Authenticatable
{
    const COVER_FOLDER = 'user_avatars';

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
        'name',
        'email',
        'password',
        'sex',
        'age',
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
        $ext = pathinfo($url, PATHINFO_EXTENSION);
        $cover = ($ext) ? $filename : md5(uniqid(time(), true)) . '.jpg';
        $this->cover = $cover;
        $manager = new Image();
        $manager->make($url)->save(storage_path('app/public/' . $this->getRealStorageCoverPath($cover)));
    }

    public function getCoverAttribute($cover = '')
    {
        if (!$cover || !Storage::exists($this->getRealStorageCoverPath($cover))) {
            return (string)$cover;
        }

        return Storage::url($this->getRealStorageCoverPath($cover));
    }

    /**
     * @param  \Illuminate\Http\UploadedFile|array|null $file
     */
    public function saveCoverByFile($file)
    {
        $fileName = md5(uniqid(time(), true)) . '.' . $file->getClientOriginalExtension();
        $this->cover = $fileName;
        Storage::putFileAs(self::COVER_FOLDER, $file, $fileName);
    }

    private function getRealStorageCoverPath($cover = '')
    {
        $cover = $cover ?: $this->cover;
        return self::COVER_FOLDER . '/' . $cover;
    }

    public function scopeOfSex($query, $sex)
    {
        return $query->where("sex", $sex);
    }

    public function scopeOfAge($query, $operand, $age)
    {
        return $query->where("age", $operand, $age);
    }

    public static function sexList()
    {
        return [self::ALIEN, self::MALE, self::FEMALE];
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function relations()
    {
        return $this->hasMany(UserRelationship::class);
    }

    /**
     * @return array
     */
    public function getBaseInfo()
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "cover" => $this->cover,
        ];
    }
}
