<?php

namespace App\Http\Controllers;

use App\HistoryConfigs\DistrictHistoryConfig;
use App\HistoryConfigs\EventHistoryConfig;
use App\HistoryConfigs\TheaterHistoryConfig;
use App\HistoryConfigs\TicketHistoryConfig;
use App\HistoryConfigs\UserHistoryConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\HistorySaving;

class FifthVersion extends Controller
{

    private static $tableName_historyConfigs = ([
        'users' => UserHistoryConfig::class,
        'tickets' => TicketHistoryConfig::class,
        'events' => EventHistoryConfig::class,
        'theaters' => TheaterHistoryConfig::class,
        'districts' => DistrictHistoryConfig::class,
    ]);

    private static function getOneFirstObject($tableName, $original_id, $is_first_created=0, $is_current=0){
        $history = HistorySaving::
        where('table_name', $tableName)
            ->where('original_id', $original_id)
            ->where('first_created', $is_first_created)
            ->orderBy('created_at');
        return $is_first_created ? $history->first() : $history;
    }

    private static function objectAndInnerRelationsFirstCreated($table, $original_id, $first_created=1)
    {
        $this_cfg = self::$tableName_historyConfigs[$table]::get_cfg();
        $main_object = self::getOneFirstObject($this_cfg['table_name'], $original_id, $first_created);
        $main_object_data = $main_object->changes;
        foreach($this_cfg['foreign_tables'] as $field => $foreign_table){
            $foreign_object = self::getOneFirstObject($foreign_table['table'], $main_object->changes[$field], $first_created);
            $inner_for_foreign_object = self::objectAndInnerRelationsFirstCreated($foreign_object->table_name, $foreign_object->changes['id'], $first_created);
            $main_object_data[$foreign_table['name']] = $inner_for_foreign_object;
        }
        return $main_object_data;
    }

    private static function getCurrentObject($table, $original_id){
        $model = self::$tableName_historyConfigs[$table]::get_cfg()['model'];
        return $model::find($original_id);
    }

    private static function objectAndInnerRelationsCurrentCreated($table, $original_id)
    {
        $this_cfg = self::$tableName_historyConfigs[$table]::get_cfg();

        $main_object = self::getCurrentObject($this_cfg['table_name'], $original_id);
        $main_object_data = $main_object->toArray();
        foreach($this_cfg['foreign_tables'] as $field => $foreign_table){
            $foreign_object = self::getCurrentObject($foreign_table['table'], $main_object->$field);
            $inner_for_foreign_object = self::objectAndInnerRelationsCurrentCreated($foreign_table['table'], $foreign_object->id);
            $main_object_data[$foreign_table['name']] = $inner_for_foreign_object;
        }
        return $main_object_data;
    }

    public function test5($table, $original_id)
    {
        $all_history = ([]);
        $created = self::objectAndInnerRelationsFirstCreated($table, $original_id, first_created: 1);
        $all_history[] = $created;
        return ([
            'all_history'=>$all_history,
            'current' => self::objectAndInnerRelationsCurrentCreated($table, $original_id)
        ]);
    }
}