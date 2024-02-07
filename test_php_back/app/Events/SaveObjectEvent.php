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
        $only_changed = array_intersect_key($changes, $original);


        $changed_fks = $this->check_if_foreign_key($object->getTable(), $only_changed);

        $newHistorySaving = HistorySaving::create([
            'table_name' => $object->getTable(),
            'changes' => json_encode($only_changed),
            'original_id' => $object->getKey(),
            'has_foreign_chagned' => (count($changed_fks) > 0)? true : false
        ]);

        foreach ($changed_fks as $fk_column => $fk_table) {
            $original_object = DB::table($fk_table)->find($original[$fk_column]);
            HistorySavingAllObject::create([
                'table_name' => $fk_table,
                'data' => json_encode(get_object_vars($original_object)),
                'original_instance_id' => $original_object->id,
                'history_change_id' => $newHistorySaving->id
            ]);
        }

    }

}
