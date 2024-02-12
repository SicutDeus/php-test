<?php

namespace App\Http\Controllers\History;

class HistoryFirstCreated extends HistoryBase
{
    public static function objectAndInnerRelationsFirstCreated($table, $original_id)
    {
        $this_cfg = self::$tableNamesHistoryConfigs[$table]::get_cfg();
        $main_object = self::getHistoryOrFirstCreatedObject($this_cfg['table_name'], $original_id);
        return self::getInnerRelationsData($main_object, $this_cfg);
    }

    private static function getInnerRelationsData($main_object, $this_cfg)
    {
        $main_object_data = $main_object->changes;
        $main_object_data['created_at'] = $main_object->change_made_at;
        foreach ($this_cfg['foreign_tables'] as $field => $foreign_table) {
            $foreign_object = self::getHistoryOrFirstCreatedObject(
                $foreign_table['table'],
                $main_object->changes[$field]
            );
            $inner_for_foreign_object = self::objectAndInnerRelationsFirstCreated(
                $foreign_object->table_name,
                $foreign_object->changes['id']
            );
            $main_object_data[$foreign_table['name']] = $inner_for_foreign_object;
        }
        return $main_object_data;
    }
}