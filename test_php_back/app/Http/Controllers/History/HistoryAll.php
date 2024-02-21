<?php

namespace App\Http\Controllers\History;

use App\Models\HistorySavingAllObject;

class HistoryAll extends HistoryBase
{
    public static function objectAndInnerRelationsHistory(
        $table,
        $original_id,
        $start_date,
        $end_date,
        &$nested,
        $current_scope,
        &$already_added_history,
        $should_not_check = [],
    ) {
        $this_cfg = self::$tableNamesHistoryConfigs[$table]::get_cfg();
        $main_object_history = self::getHistoryOrFirstCreatedObject(
            $this_cfg['table_name'],
            $original_id,
            0,
            $start_date,
            $end_date
        );
        $resulting_data = array();
        foreach ($main_object_history as $history_object) {
            HistoryManyToMany::getManyToManyHistory($history_object, $nested, $current_scope, $already_added_history);
            if (!array_key_exists($history_object->id, $already_added_history)) {
                $inner_data = ([]);
                foreach ($this_cfg['foreign_tables'] as $field => $foreign_table) {
                    if (array_key_exists($field, $should_not_check)){
                        continue;
                    }
                    $scope = $current_scope;
                    if (array_key_exists($field, $history_object->changes)) {
                        $foreign_object = self::getHistoryOrFirstCreatedObject(
                            $foreign_table['table'],
                            $history_object->changes[$field]
                        );
                        $scope[] = $foreign_table['name'];
                        self::objectAndInnerRelationsHistory(
                            $foreign_object->table_name,
                            $foreign_object->changes['id'],
                            $start_date,
                            $end_date,
                            $nested,
                            $scope,
                            $already_added_history
                        );
                        $obj_image = HistorySavingAllObject::
                        where('history_change_id', $history_object->id)
                            ->where('table_name', $foreign_table['table'])
                            ->first();
                        if ($obj_image) {
                            $inner_data[$foreign_table['name']] = $obj_image->new_object_data;
                        }
                    }
                }
                if (!$history_object->first_created) {
                    $dataToAdd = $history_object->changes;
                    $dataToAdd['id'] = $history_object->original_id;
                    $res = array_merge(
                        $inner_data,
                        $dataToAdd,
                        ['change_made_at' => $history_object->change_made_at]
                    );
                    $resulting_data = $res;
                    self::setVal($nested, $current_scope, $res);

                    $already_added_history[$history_object->id] = true;
                }
            }
        }
        return $resulting_data;
    }
}
