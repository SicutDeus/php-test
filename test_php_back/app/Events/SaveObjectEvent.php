<?php

namespace App\Events;

use App\Models\HistorySaving;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SaveObjectEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct($object)
    {
        $original = $object->getOriginal();
        $changes = $object->getChanges();
        $duplicates = array_intersect_key($changes, $original);

//        dd([
//            'original' => $original,
//            'changes' => $changes,
//            'duplicates' => $duplicates,
//            'table_name' => $object->getTable()
//        ]);
        HistorySaving::create([
            'table_name' => $object->getTable(),
            'changes' => json_encode($duplicates),
            'original_id' => $object->getKey()
        ]);
    }

}
