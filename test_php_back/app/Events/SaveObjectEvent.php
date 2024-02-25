<?php

namespace App\Events;

use App\HistoryConfigs\HistoryBaseConfig;
use App\Http\Controllers\FifthVersion;
use App\Http\Controllers\History\HistoryBase;
use App\Http\Controllers\History\HistoryCurrent;
use App\Models\HistorySaving;
use App\Models\HistorySavingAllObject;
use App\Models\Ticket;
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

class SaveObjectEvent
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
                $associated_name = 'saved';
                if (array_key_exists($field, $object->getChanges())){
                    $associated_name = 'changed';
                    $removed_relation = HistoryBase::$tableNamesHistoryConfigs[$table]::get_cfg()['model']::find($object->getOriginal($field));
                }
                $related_objs[$associated_name] = HistoryBase::$tableNamesHistoryConfigs[$table]::get_cfg()['model']::find($object->getAttribute($field));
            }

            self::createManyToManyHistory($removed_relation,  $related_objs['saved'], 'removed');
            self::createManyToManyHistory($related_objs['changed'],  $related_objs['saved'], 'added');
        }
        else{
            $original = $object->getOriginal();
            $changes = $object->getChanges();
            $only_changed = array_diff_key(
                array_intersect_key($changes, $original),
                array_flip(['created_at', 'updated_at'])
            );

            $changed_fks = $this->checkIfForeignKey($object->getTable(), $only_changed);
            $newHistorySaving = self::createOneObjectHistory($object, $only_changed, false);
            $this_cfg = HistoryBase::$tableNamesHistoryConfigs[$object->getTable()]::get_cfg();
            foreach ($changed_fks as $field => $fk_table) {
                if (array_key_exists($field, $this_cfg['oneToMany'])){
                    $related_obj = HistoryBase::$tableNamesHistoryConfigs[$this_cfg['oneToMany'][$field]]::get_cfg()['model']::find($changes[$field]);
                    $deleted_obj = HistoryBase::$tableNamesHistoryConfigs[$this_cfg['oneToMany'][$field]]::get_cfg()['model']::find($original[$field]);
                    self::createManyToManyHistory($object,  $related_obj, 'added');
                    self::createManyToManyHistory($object,  $deleted_obj, 'removed');
                }
                $original_object = array_diff_key(
                     HistoryBase::$tableNamesHistoryConfigs[$fk_table]
                        ::get_cfg()['model']
                        ::find($original[$field])
                        ->toArray(),
                    array_flip(['created_at', 'updated_at'])
                );
                $new_object = self::createCurrentAllObject($fk_table, $changes[$field]);
                HistorySavingAllObject::create([
                    'table_name' => $fk_table,
                    'old_object_data' => $original_object,
                    'new_object_data' => array_diff_key($new_object, ['created_at', 'updated_at']),
                    'original_instance_id' => $original_object['id'],
                    'history_change_id' => $newHistorySaving['id'],
                ]);
        }
    }
    }
}
