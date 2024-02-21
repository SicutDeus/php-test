<?php

namespace App\Models;

use App\Events\CreateObjectEvent;
use App\Events\SaveObjectEvent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seller extends Model
{
    use HasFactory;
    protected $guarded = false;

    protected $dispatchesEvents = [
        'updated' => SaveObjectEvent::class,
        'created' => CreateObjectEvent::class,
    ];
}
