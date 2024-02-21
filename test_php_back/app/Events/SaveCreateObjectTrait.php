<?php

namespace App\Events;

use App\Models\HistorySaving;
use App\Models\HistorySavingManyToMany;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

trait SaveCreateObjectTrait
{
    private function checkIfForeignKey($table, $changed)
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

    private static function createManyToManyHistory($first_obj, $second_obj, $status)
    {
        if($status === 'removed') {
            $toAddExpireTime = HistorySavingManyToMany
            ::where('first_table', $first_obj->getTable())
                ->where('second_table', $second_obj->getTable())
                ->where('first_id', $first_obj->id)
                ->where('second_id', $second_obj->id)
                ->where('status', 'added')
                ->where('expired_at', null)->first();
            $toAddExpireTime->expired_at = Carbon::now()->subSeconds(5)->format('Y-m-d H:i:s.u');
            $toAddExpireTime->save();
        }
        HistorySavingManyToMany::create([
            'first_table' => $first_obj->getTable(),
            'second_table' => $second_obj->getTable(),
            'first_id' => $first_obj->id,
            'second_id' => $second_obj->id,
            'status' => $status,
            'change_made_at' => Carbon::now()->format('Y-m-d H:i:s.u'),
        ]);
    }

    private static function createOneObjectHistory($object, $changes, $first_created=false){
        return HistorySaving::create([
            'table_name' => $object->getTable(),
            'changes' => $changes,
            'original_id' => $object->getKey(),
            'change_made_at' => Carbon::now()->format('Y-m-d H:i:s.u'),
            'first_created' => $first_created,
        ]);
    }
}