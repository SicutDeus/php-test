<?php

namespace App\Http\Controllers;

use App\HistoryConfigs\DistrictHistoryConfig;
use App\HistoryConfigs\EventHistoryConfig;
use App\HistoryConfigs\TheaterHistoryConfig;
use App\HistoryConfigs\TicketHistoryConfig;
use App\HistoryConfigs\UserHistoryConfig;
use App\Models\HistorySavingAllObject;
use DateInterval;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\HistorySaving;
use stdClass;

class FifthVersion extends Controller
{
    private static $date_format = 'Y-m-d H:i:s.u';
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

    private static function getHistoryOrFirstCreatedObject($tableName, $original_id, $is_first_created=1, $start_date=Null, $end_date=Null){
        $history = HistorySaving::
        where('table_name', $tableName)
            ->where('original_id', $original_id)
//            ->where('first_created', $is_first_created)
            ->orderBy('created_at', 'ASC');
        if ($start_date && $end_date) {
            $history = $history->whereBetween('created_at', [$start_date, $end_date]);
        }
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

    private static function addMiliseconds($date)
    {
        $interval = new DateInterval('PT0S');
        $interval->f = random_int(0, 999999) / 1000000;
        $date->add($interval);
        return $date;
    }

    private static function setVal(&$data,$scope,$value){
        $date = self::addMiliseconds(date_create($value['updated_at']));
        $level = &$data[$date->format(self::$date_format)];
        $len = count($scope);
        for($i=0;$i<$len;$i++) {
            $level = &$level[$scope[$i]];
        }
        if($level == Null){
            $level = $value;
        }
        else{
            $level[] = $value;
        }
    }

    private static function objectAndInnerRelationsHistory($table, $original_id, $start_date, $end_date, &$nested, $current_scope)
    {
        $this_cfg = self::$tableName_historyConfigs[$table]::get_cfg();
        $main_object_history = self::getHistoryOrFirstCreatedObject($this_cfg['table_name'], $original_id, 0, $start_date, $end_date);
        $resulting_data = array();
        foreach ($main_object_history as $history_object) {
            $inner_data = ([]);
            foreach ($this_cfg['foreign_tables'] as $field => $foreign_table) {
                $scope = $current_scope;
                if (array_key_exists($field, $history_object->changes)) {
                    $foreign_object = self::getHistoryOrFirstCreatedObject($foreign_table['table'], $history_object->changes[$field]);
                    $scope[] = $foreign_table['name'];
                    $inner_for_foreign_object = self::objectAndInnerRelationsHistory(
                        $foreign_object->table_name,
                        $foreign_object->changes['id'],
                        $start_date, $end_date,
                        $nested,
                        $scope
                    );
                    $obj_image = HistorySavingAllObject::
                    where('history_change_id', $history_object->id)
                    ->where('table_name', $foreign_table['table'])
                    ->first();
                    if ($obj_image){
                        $inner_data[$foreign_table['name']] = $obj_image->new_object_data;
                    }
                }
            }
            if (!$history_object->first_created) {
                $res = array_merge($inner_data, $history_object->changes);
                $resulting_data = $res;
                self::setVal($nested, $current_scope, $res);
            }
        }
        return $resulting_data;
    }

    public function test5($table, $original_id)
    {
        $all_history = ([]);
        $this_cfg = self::$tableName_historyConfigs[$table]::get_cfg();
        $created = self::objectAndInnerRelationsFirstCreated($table, $original_id);
        $current = self::objectAndInnerRelationsCurrentCreated($table, $original_id);
        $main_object = self::getCurrentObject($table, $original_id);
        $start_time = $main_object->getAttributes()['created_at'];
        $end_time = self::getEndInterval($start_time, 1920);
        $nested = ([]);
        self::objectAndInnerRelationsHistory($table, $original_id, '2020-02-01 16:43:25', '2030-02-01 16:43:25', $nested, [$this_cfg['front_one_name']]);

        $nested[self::addMiliseconds(date_create($created['created_at']))->format(self::$date_format)]= ([$this_cfg['front_one_name'] => array($created)]);
        $nested[now()->format(self::$date_format)] = ([$this_cfg['front_one_name'] => array($current)]);
        ksort($nested);

        return ([
            'all_history'=>self::combine_arrays_by_time($nested),
        ]);
    }

    private static function getEndInterval($start_date, $hours){
        $end_date = new \DateTime($start_date);
        $end_date->add(new \DateInterval('PT'.$hours.'H'));
        return $end_date->format(self::$date_format);
    }

    private static function combine_arrays_by_time($input) {
        $result = [];
        $temp = [];
        $MS_IN_S = 60;
        $MERGE_THRESHOLD = 1/60; // in seconds

        foreach ($input as $time => $array) {
            if (empty($temp)) {
                $temp[$time] = $array;
            } else {
                $last_time = key(array_slice($temp, -1, 1, true));
                $diff = abs(strtotime($time) - strtotime($last_time));

                if ($diff <= $MERGE_THRESHOLD * $MS_IN_S) {
                    $temp[$time] = $array;
                } else {
                    $result[] = $temp;
                    $temp = [$time => $array];
                }
            }
        }

        if (!empty($temp)) {
            $result[] = $temp;
        }

        return $result;
    }
}