<?php

namespace App\Models;

use App\Events\SaveObjectEvent;
use App\TestModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends TestModel
{
    use HasFactory;

    protected $dispatchesEvents = [
        'updated' => SaveObjectEvent::class,
    ];
}
