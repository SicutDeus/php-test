<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Image extends Model
{
    use HasFactory;
    public static function getPathOfImage(string $filename = null, string $user_id = null)
    {
        $patch = null;
        if ($user_id !== null) {
            $patch = $user_id.'/';
        }
        return 'storage/uploads/'.$patch.$filename;
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function getUrlAttribute()
    {
        return asset(self::getPathOfImage($this->hash));
    }
}
