<?php

namespace App\Models;

use App\Events\CreateObjectEvent;
use App\Events\SaveObjectEvent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    use HasFactory;
    protected $dispatchesEvents = [
        'updated' => SaveObjectEvent::class,
        'created' => CreateObjectEvent::class,
    ];
    public function theaters(){
        return $this->hasMany(Theater::class);
    }
}
