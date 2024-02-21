<?php

namespace App\HistoryConfigs;

class HistoryBaseConfig{
    protected static $table_name; // this model table name
    protected static $model; // this model
    protected static $front_many_name; // this model
    protected static $front_one_name; // this model
    protected static $relations = ([]); // what relations should be get -> ['relation_name' => 'method_to_get_relation']
    protected static $foreign_tables = ([]); // what relations should be get -> ['relation_field' => 'relative_table']
    protected static $exclude_fields = ([]); // if some fields can be excluded
    protected static $extra_filter_fields = ([]); // ['field_name' => 'field_value']
    protected static $timestamp_to_filter = ([]); // ['field_name' => 'timestamp']

    protected static $manyToMany = [];

    protected static $oneToMany = ([]);
    protected static $fromOtherTables = ([]);



    public static function get_cfg(){
        return [
            'table_name' => static::$table_name,
            'model' => static::$model,
            'front_many_name' => static::$front_many_name,
            'front_one_name' => static::$front_one_name,
            'relations' => static::$relations,
            'exclude_fields' => static::$exclude_fields,
            'extra_filter_fields' => static::$extra_filter_fields,
            'timestamps_to_filter' => static::$timestamp_to_filter,
            'foreign_tables' => static::$foreign_tables,
            'manyToMany' => static::$manyToMany,
            'oneToMany' => static::$oneToMany,
            'fromOtherTables' => static::$fromOtherTables,
        ];
    }

    public static $all_many_to_many_rels = ([
        'seller_appeals' => ([
            'seller_id' => 'sellers',
            'appeal_id' => 'appeals',
        ])
    ]);
}