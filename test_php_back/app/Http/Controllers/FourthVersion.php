<?php

namespace App\Http\Controllers;

use App\HistoryConfigs\EventHistoryConfig;
use App\HistoryConfigs\HistoryBaseConfig;
use App\HistoryConfigs\TicketHistoryConfig;
use App\HistoryConfigs\UserHistoryConfig;
use App\Models\HistorySavingAllObject;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FourthVersion extends Controller
{
    private static $tableName_historyConfigs = ([
        'users' => UserHistoryConfig::class,
        'tickets' => TicketHistoryConfig::class,
        'events' => EventHistoryConfig::class,
    ]);

    private static function get_fk_table($table, $field, &$fk_table_name){
        return
            collect(Schema::getConnection()
            ->getDoctrineSchemaManager()
            ->listTableForeignKeys($table))
            ->map(function ($fkColumn) use ($field, &$fk_table_name) {
                if((string)$fkColumn->getLocalColumns()[0] === $field){
                    $fk_table_name = (string)$fkColumn->getForeignTableName();
                };
            })->flatten();
    }

    private static function getOneTable($table, $original_id=Null, $object=Null, &$all_history=([]))
    {
        $data = ([]);
        $cfg = self::$tableName_historyConfigs[$table]::get_cfg();
        if ($object === Null) {
            $object = $cfg['model']::find($original_id);
        }
        $main_object_data = $object->toArray();

        foreach ($cfg['relations'] as $name => $method) {
            $relation_data = ([]);
            $relations = $object->$method;
            if (get_class($relations) != Collection::class) {
                $tmp = $relations;
                $relations = new Collection();
                $relations->add($tmp);
            }
            foreach ($relations as $relation) {
                $tmp = self::getOneTable($name, object: $relation, all_history: $all_history);
                $relation_data[] = $tmp;
            }
            $main_object_data[$name] = $relation_data;
        }

        foreach ($cfg['exclude_fields'] as $field) {
            unset($main_object_data[$field]);
        }
        $data[$cfg['front_many_name']]['object'] = $main_object_data;
        $history = DB::table('history_savings')
            ->where('table_name', $table)
            ->where('original_id', $object->id)
            ->get();
        foreach ($history as $index=>$item) {
            error_log('asddd');
            foreach(json_decode($item->changes) as $field => $value) {
                if (str_ends_with($field, '_id')) {
                    self::get_fk_table($table, $field, $fk_table_name);
                    $saved_all_object = HistorySavingAllObject::all()
                        ->where('table_name', $fk_table_name)
                        ->where('history_change_id', $item->id)
                        ->first();
                    $field_name = 'previous_'.str_replace('_id', '', $field);
                    $history[$index]->$field_name = $saved_all_object->getOriginal()['data'];
                }
            }
        }
        $data[$cfg['front_many_name']]['history'] = $history;
        $all_history[] = $history;
        return $data;
    }
    public function jopaTest($table, $original_id)
    {
        $all_history = ([]);
        $res = self::getOneTable($table, original_id: $original_id, all_history: $all_history);
        $check = [];
        foreach ($all_history as $history) {
            foreach ($history as $item) {
                $check[] = $item;
            }
        }
        usort($check, function ($a, $b) { return $a->created_at <=> $b->created_at; });
        $res['all_history'] = $check;
        return $res;
    }
}
