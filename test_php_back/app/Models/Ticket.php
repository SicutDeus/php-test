<?php

namespace App\Models;

use App\Events\SaveObjectEvent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    use HasFactory;
    protected $dispatchesEvents = [
        'updated' => SaveObjectEvent::class,
    ];

    public function event()
    {
        return $this->hasOne(Event::class, 'id', 'event_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

}
