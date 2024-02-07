<?php

namespace App\Models;

use App\Events\SaveObjectEvent;
use App\Events\SaveObjectEventTest;
use App\HistorySavingsConfig;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $dispatchesEvents = [
//        'updated' => SaveObjectEvent::class,
        'updating' => SaveObjectEvent::class,
    ];
}
