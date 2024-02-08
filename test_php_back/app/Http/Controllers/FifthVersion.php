<?php

namespace App\Http\Controllers;

use App\HistoryConfigs\DistrictHistoryConfig;
use App\HistoryConfigs\EventHistoryConfig;
use App\HistoryConfigs\TheaterHistoryConfig;
use App\HistoryConfigs\TicketHistoryConfig;
use App\HistoryConfigs\UserHistoryConfig;
use Illuminate\Database\Eloquent\Builder;
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

    private static function getHistoryOrFirstCreatedObject($tableName, $original_id, $is_first_created=1){
        $history = HistorySaving::
        where('table_name', $tableName)
            ->where('original_id', $original_id)
            ->where('first_created', $is_first_created)
            ->orderBy('created_at', 'ASC');
        return $is_first_created ? $history->first() : $history->get();
    }

    private static function objectAndInnerRelationsFirstCreated($table, $original_id)
    {
        $this_cfg = self::$tableName_historyConfigs[$table]::get_cfg();
        $main_object = self::getHistoryOrFirstCreatedObject($this_cfg['table_name'], $original_id);
        return self::getInnerRelationsData($main_object, $this_cfg);
    }

    private static function getInnerRelationsData($main_object, $this_cfg)
    {
        $main_object_data = $main_object->changes;
        foreach($this_cfg['foreign_tables'] as $field => $foreign_table){
            $foreign_object = self::getHistoryOrFirstCreatedObject($foreign_table['table'], $main_object->changes[$field]);
            $inner_for_foreign_object = self::objectAndInnerRelationsFirstCreated($foreign_object->table_name, $foreign_object->changes['id']);
            $main_object_data[$foreign_table['name']] = $inner_for_foreign_object;
        }
        return $main_object_data;
    }

    private static function objectAndInnerRelationsHistory($table, $original_id){
        $this_cfg = self::$tableName_historyConfigs[$table]::get_cfg();
        $main_object_history = self::getHistoryOrFirstCreatedObject($this_cfg['table_name'], $original_id, is_first_created: 0);
        $history = array();
        foreach($main_object_history as $history_object){
            $inner_data = ([]);
            foreach($this_cfg['foreign_tables'] as $field => $foreign_table) {
                if (array_key_exists($field, $history_object->changes)) {
                    $foreign_object = self::getHistoryOrFirstCreatedObject($foreign_table['table'], $history_object->changes[$field]);
                    $inner_for_foreign_object = self::objectAndInnerRelationsHistory($foreign_object->table_name, $foreign_object->changes['id']);
                    $inner_data[$foreign_table['name']] = $inner_for_foreign_object;
                }
            }
            $result = array_merge($history_object->changes, $inner_data);
            $history[] = $result;
        }
        return $history;
    }




    public function test5($table, $original_id)
    {
        $all_history = ([]);
        $created = self::objectAndInnerRelationsFirstCreated($table, $original_id);
        $all_history[] = $created;
        return ([
            'all_history'=>$all_history,
            'current' => self::objectAndInnerRelationsCurrentCreated($table, $original_id),
            'history' => self::objectAndInnerRelationsHistory($table, $original_id),
        ]);
    }
}