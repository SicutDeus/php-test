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
        $data = ([
            'change_made_at' => $oneRelation['change_made_at'],
            'status' => $oneRelation['status'],
        ]);
        if ($oneRelation['first_table'] === $self_table_name){
            $data['table'] = $oneRelation['second_table'];
            $data['id'] = $oneRelation['second_id'];
        }
        else{
            $data['table'] = $oneRelation['first_table'];
            $data['id'] = $oneRelation['first_id'];
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
            $readable = $isManyToMany ? self::makeReadableContentManyToMany($oneRelation->toArray(), $history_obj->table_name)
             : self::makeReadableContentManyToMany($oneRelation->toArray(), $history_obj->table_name);
            $shouldNotCheck = $isManyToMany ? [] : self::getRecursionForeignField($oneRelation->toArray(), $history_obj->table_name);
            HistoryBase::setVal(
                $nested,
                $inner_scope,
                $readable
            );
            if ($readable['status'] === 'added'){
                HistoryAll::objectAndInnerRelationsHistory(
                    $readable['table'],
                    $readable['id'],
                    $oneRelation->change_made_at,
                    $oneRelation->expired_at,
                    $nested,
                    $inner_scope,
                    $already_added_history,
                    $shouldNotCheck
                );
                }
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