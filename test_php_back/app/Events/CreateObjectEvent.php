<?php

namespace App\Events;

use App\HistoryConfigs\HistoryBaseConfig;
use App\Http\Controllers\History\HistoryBase;
use App\Models\HistorySaving;
use App\Models\HistorySavingAllObject;
use App\Models\HistorySavingManyToMany;
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
    use Dispatchable, InteractsWithSockets, SerializesModels, SaveCreateObjectTrait;

    /**
     * Create a new event instance.
     */

    public function __construct($object)
    {
        if (array_key_exists($object->getTable(), HistoryBaseConfig::$all_many_to_many_rels)){
            $relation = HistoryBaseConfig::$all_many_to_many_rels[$object->getTable()];
            $related_objs = [];
            foreach ($relation as $field => $table){
                $related_objs[] = HistoryBase::$tableNamesHistoryConfigs[$table]::get_cfg()['model']::find($object->getAttribute($field));
            }
            self::createManyToManyHistory($related_objs[0],  $related_objs[1], 'added');
        }
        else{
            $this_cfg = HistoryBase::$tableNamesHistoryConfigs[$object->getTable()]::get_cfg();
            foreach ($this_cfg['oneToMany'] as $field => $table){
                HistorySavingManyToMany::create([
                    'first_table' => $object->getTable(),
                    'first_id' => $object->id,
                    'second_table' => $table,
                    'second_id' => $object->getAttribute($field),
                    'first_data' => self::createCurrentAllObject($object->getTable(), $object->id),
                    'second_data' => self::createCurrentAllObject($table, $object->getAttribute($field)),
                    'change_made_at' => Carbon::now()->format('Y-m-d H:i:s.u'),
                ]);
            }

            self::createOneObjectHistory(
                $object,
                array_diff_key(
                    $object->getAttributes(),
                    array_flip(['password', 'remember_token', 'created_at', 'updated_at'])
                ),
                true
            );
        }
    }

}
