<?php

namespace App\Http\Controllers\History;

use App\Http\Controllers\History\HistoryBase;

class HistoryCurrent extends HistoryBase
{
    private static function getCurrentObject($table, $original_id)
    {
        $model = self::$tableNamesHistoryConfigs[$table]::get_cfg()['model'];
        return $model::find($original_id);
    }

    public static function objectAndInnerRelationsCurrentObject($table, $original_id)
    {
        $this_cfg = self::$tableNamesHistoryConfigs[$table]::get_cfg();
        $main_object = self::getCurrentObject($this_cfg['table_name'], $original_id);
        $main_object_data = $main_object->toArray();
        foreach ($this_cfg['manyToMany'] as $many_foreign_table => $many_foreign_method) {
            $foreign_objs = $main_object->$many_foreign_method;
            foreach ($foreign_objs as $foreign){
                $inner_for_foreign_object = self::objectAndInnerRelationsCurrentObject(
                    $many_foreign_table,
                    $foreign->id
                );
                $main_object_data[$many_foreign_table][] = $inner_for_foreign_object;
            }
        }
        foreach ($this_cfg['fromOtherTables'] as $table_name => $method) {
            $foreign_objs = $main_object->$method;
            foreach ($foreign_objs as $foreign){
                $inner_for_foreign_object = self::objectAndInnerRelationsCurrentObject(
                    $table_name,
                    $foreign->id
                );
                $main_object_data[$table_name][] = $inner_for_foreign_object;
            }
        }
        foreach ($this_cfg['foreign_tables'] as $field => $foreign_table) {
            $foreign_object = self::getCurrentObject($foreign_table['table'], $main_object->$field);
            $inner_for_foreign_object = self::objectAndInnerRelationsCurrentObject(
                $foreign_table['table'],
                $foreign_object->id
            );
            $main_object_data[$foreign_table['name']] = $inner_for_foreign_object;
        }
        return $main_object_data;
    }
}