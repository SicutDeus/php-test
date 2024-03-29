<?php

namespace App\Http\Controllers;

use App\HistoryConfigs\EventHistoryConfig;
use App\HistoryConfigs\HistoryBaseConfig;
use App\HistoryConfigs\TicketHistoryConfig;
use App\HistoryConfigs\UserHistoryConfig;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SecondVersionClasses extends Controller
{
    private static $tableName_historyConfigs = ([
        'users' => UserHistoryConfig::class,
        'tickets' => TicketHistoryConfig::class,
        'events' => EventHistoryConfig::class,
    ]);

    private static function getOneTable($table, $original_id=Null, $object=Null)
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
                $tmp = self::getOneTable($name, object: $relation);
                $relation_data[] = $tmp;
            }
            $main_object_data[$name] = $relation_data;
        }

        foreach ($cfg['exclude_fields'] as $field) {
            unset($main_object_data[$field]);
        }
        $data[$cfg['front_one_name']]['object'] = $main_object_data;
        $data[$cfg['front_one_name']]['history'] =
            DB::table('history_savings')
            ->where('table_name', $table)
            ->where('original_id', $object->id)
            ->get()->toArray();
        return $data;
    }
    public function testWithClasses($table, $original_id)
    {
        return self::getOneTable($table, original_id: $original_id);

    }
}
