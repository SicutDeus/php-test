<?php

namespace App\Models;

use App\Events\CreateObjectEvent;
use App\Events\SaveObjectEvent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Theater extends Model
{
    use HasFactory;
    protected $dispatchesEvents = [
        'updated' => SaveObjectEvent::class,
        'created' => CreateObjectEvent::class,
    ];
    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function district(){
        return $this->belongsTo(District::class);
    }
}
