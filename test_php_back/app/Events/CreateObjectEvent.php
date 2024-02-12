<?php

namespace App\Events;

use App\Models\HistorySaving;
use App\Models\HistorySavingAllObject;
use Carbon\Carbon;
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
            'changes' => array_diff_key(
                $object->getAttributes(),
                array_flip(['password', 'remember_token', 'created_at', 'updated_at'])
            ),
            'original_id' => $object->getKey(),
            'first_created' => true,
            'change_made_at' => Carbon::now()->format('Y-m-d H:i:s.u'),
        ]);
    }

}
