<?php

namespace App\Http\Controllers;

use App\HistoryConfigs\EventHistoryConfig;
use App\HistoryConfigs\HistoryBaseConfig;
use App\HistoryConfigs\TicketHistoryConfig;
use App\HistoryConfigs\UserHistoryConfig;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ThirdVersionAllHistory extends Controller
{
    private static $tableName_historyConfigs = ([
        'users' => UserHistoryConfig::class,
        'tickets' => TicketHistoryConfig::class,
        'events' => EventHistoryConfig::class,
    ]);

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
        $data[$cfg['front_many_name']]['history'] =
            DB::table('history_savings')
            ->where('table_name', $table)
            ->where('original_id', $object->id)
            ->get()->toArray();
        $all_history[] = $data[$cfg['front_many_name']]['history'];
        return $data;
    }
    public function testWithClasses($table, $original_id)
    {
        $all_history = ([]);
        $res = self::getOneTable($table, original_id: $original_id, all_history: $all_history);
        $all_history[] = reset($res)['history'];
        dd($all_history);

    }
}
