<?php

namespace App\Events;

use App\Models\HistorySaving;
use App\Models\HistorySavingAllObject;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use function Symfony\Component\Translation\t;

class CreateObjectEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */

    public function __construct($object)
    {
        HistorySaving::create([
            'table_name' => $object->getTable(),
            'changes' => $object->getAttributes(),
            'original_id' => $object->getKey(),
            'has_foreign_chagned' => false,
            'first_created' => true,
        ]);

    }

}
