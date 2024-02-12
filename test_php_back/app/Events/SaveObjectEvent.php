<?php

namespace App\Events;

use App\Http\Controllers\FifthVersion;
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
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    private function check_if_foreign_key($table, $changed)
    {
        $fkColumns = array_merge(
            ...collect(Schema::getConnection()
            ->getDoctrineSchemaManager()
            ->listTableForeignKeys($table))
            ->map(function ($fkColumn) {
                return ([
                    (string)$fkColumn->getColumns()[0] => (string)$fkColumn->getForeignTableName()
                ]);
            })
        );
        return array_intersect_key($fkColumns, $changed);
    }

    public function __construct($object)
    {
        $original = $object->getOriginal();
        $changes = $object->getChanges();
        $only_changed = array_diff_key(
            array_intersect_key($changes, $original),
            array_flip(['created_at', 'updated_at'])
        );

        $changed_fks = $this->check_if_foreign_key($object->getTable(), $only_changed);


        $newHistorySaving = HistorySaving::create([
            'table_name' => $object->getTable(),
            'changes' => $only_changed,
            'original_id' => $object->getKey(),
            'change_made_at' => Carbon::now()->format('Y-m-d H:i:s.u'),
        ]);
        foreach ($changed_fks as $fk_column => $fk_table) {
//            $original_object = DB::table($fk_table)->find($original[$fk_column]);
            $original_object = array_diff_key(
                FifthVersion::$tableName_historyConfigs[$fk_table]
                    ::get_cfg()['model']
                    ::find($original[$fk_column])
                    ->toArray(),
                array_flip(['created_at', 'updated_at'])
            );
            $new_object = array_diff_key(
                FifthVersion::objectAndInnerRelationsCurrentCreated($fk_table, $changes[$fk_column]),
                array_flip(['created_at', 'updated_at'])
            );
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
