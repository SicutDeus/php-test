<?php

namespace App\Http\Controllers\History;

use App\Models\HistorySavingManyToMany;

class HistoryManyToMany extends HistoryBase
{
    private static function getOneManyToManyRelated($history_obj, $foreign_table_name){
        $history = HistorySavingManyToMany::
        where(
            function ($query) use ($history_obj, $foreign_table_name) {
                $query->where('first_table', $history_obj->table_name)
                    ->where('first_id', $history_obj->original_id)
                    ->where('second_table', $foreign_table_name);
        })->orWhere(
            function($query) use ($history_obj, $foreign_table_name)  {
                $query->where('second_table', $history_obj->table_name)
                    ->where('second_id', $history_obj->original_id)
                    ->where('first_table', $foreign_table_name);
        })->get();
        return $history;
    }
    private static function makeReadableContentManyToMany($oneRelation, $self_table_name){
        $data = ([]);
        $data['added'] = ([
            'change_made_at' => $oneRelation['change_made_at'],
            'status' => 'added'
        ]);
        if ($oneRelation['first_table'] === $self_table_name){
            $data['added']['table'] = $oneRelation['second_table'];
            $data['added']['id'] = $oneRelation['second_id'];
            $data['added'][HistoryBase::$tableNamesHistoryConfigs[$data['added']['table']]::get_cfg()['front_one_name']] = $oneRelation['second_data'];
            if ($oneRelation['expired_at']){
                $data['removed'] = ([
                    'table' => $oneRelation['second_table'],
                    'id' => $oneRelation['second_id'],
                    'status' => 'removed',
                    'change_made_at' => $oneRelation['change_made_at'],
                ]);
            }
        }
        else{
            $data['added']['table'] = $oneRelation['first_table'];
            $data['added']['id'] = $oneRelation['first_id'];
            $data['added'][HistoryBase::$tableNamesHistoryConfigs[$data['added']['table']]::get_cfg()['front_one_name']] = $oneRelation['first_data'];
            if ($oneRelation['expired_at']){
                $data['removed'] = ([
                    'table' => $oneRelation['first_table'],
                    'id' => $oneRelation['first_id'],
                    'status' => 'removed',
                    'change_made_at' => $oneRelation['expired_at'],
                ]);
            }
        }
        return $data;
    }

    private static function getRecursionForeignField($oneRelation, $self_table_name)
    {
        if ($oneRelation['first_table'] !== $self_table_name){
            return $oneRelation['first_table'];
        }
        return $oneRelation['second_table'];
    }

    private static function everyRelation($history_obj, $foreign_table_name, &$nested, $inner_scope, &$already_added_history, $isManyToMany){
        $inner_scope[] = $foreign_table_name;
        $oneRelationHistory = self::getOneManyToManyRelated($history_obj, $foreign_table_name);
        foreach ($oneRelationHistory as $oneRelation){
            $readable = self::makeReadableContentManyToMany($oneRelation->toArray(), $history_obj->table_name);
            $shouldNotCheck = $isManyToMany ? [] : self::getRecursionForeignField($oneRelation->toArray(), $history_obj->table_name);
            HistoryBase::setVal(
                $nested,
                $inner_scope,
                $readable['added']
            );
            if (array_key_exists('removed', $readable)){
                HistoryBase::setVal(
                    $nested,
                    $inner_scope,
                    $readable['removed']
                );
            }
            HistoryAll::objectAndInnerRelationsHistory(
                $readable['added']['table'],
                $readable['added']['id'],
                $oneRelation->change_made_at,
                $oneRelation->expired_at,
                $nested,
                $inner_scope,
                $already_added_history,
                $shouldNotCheck
            );
        }
    }

    public static function getManyToManyHistory($history_obj, &$nested, $scope, &$already_added_history){
        $cfg = HistoryBase::$tableNamesHistoryConfigs[$history_obj->table_name]::get_cfg();
        $inner_scope = $scope;
        foreach ($cfg['manyToMany'] as $foreign_table_name => $foreign_table_method) {
            self::everyRelation($history_obj, $foreign_table_name, $nested, $inner_scope, $already_added_history , 1);
        }
        foreach ($cfg['fromOtherTables'] as $oneRelTableName){
            self::everyRelation($history_obj, $oneRelTableName, $nested, $inner_scope, $already_added_history, 0);
        }

    }
}