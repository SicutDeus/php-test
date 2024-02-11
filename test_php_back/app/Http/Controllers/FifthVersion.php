<?php

namespace App\Http\Controllers;

use App\HistoryConfigs\DistributorHistoryConfig;
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
    private static $test_arr = [];
    private static $tableName_historyConfigs = ([
        'users' => UserHistoryConfig::class,
        'tickets' => TicketHistoryConfig::class,
        'events' => EventHistoryConfig::class,
        'theaters' => TheaterHistoryConfig::class,
        'districts' => DistrictHistoryConfig::class,
        'distributors' => DistributorHistoryConfig::class
    ]);

    private static function getCurrentObject($table, $original_id){
        $model = self::$tableName_historyConfigs[$table]::get_cfg()['model'];
        return $model::find($original_id);
    }

    public static function objectAndInnerRelationsCurrentCreated($table, $original_id)
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
            ->orderBy('created_at', 'ASC');
        if ($start_date && $end_date) {
            $history = $history->whereBetween('created_at', [$start_date, $end_date]);
        }
        return $is_first_created ? $history->where('first_created', $is_first_created)->first() : $history->get();
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
            for($i=0;$i<$len;$i++) { $level = &$level[$scope[$i]]; }
            $level = $value;
    }

    private static function get_many_to_many_realtions_history($many_relation, $original_id){
        $table = $many_relation['through_model'];
        dd($table::find($original_id));
        $t = HistorySaving::where('table_name', $many_relation['through_table'])->get();
        dd($t);
    }

    private static function objectAndInnerRelationsHistory($table, $original_id, $start_date, $end_date, &$nested, $current_scope, &$already_added_history)
    {
        $this_cfg = self::$tableName_historyConfigs[$table]::get_cfg();
        $main_object_history = self::getHistoryOrFirstCreatedObject($this_cfg['table_name'], $original_id, 0, $start_date, $end_date);
        $resulting_data = array();
        foreach ($main_object_history as $history_object) {
            if  (!array_key_exists($history_object->id, $already_added_history)) {
                $inner_data = ([]);
                foreach ($this_cfg['foreign_tables'] as $field => $foreign_table) {
                    $scope = $current_scope;
                    if (array_key_exists($field, $history_object->changes)) {
                        $foreign_object = self::getHistoryOrFirstCreatedObject($foreign_table['table'], $history_object->changes[$field]);
                        $scope[] = $foreign_table['name'];
                        self::objectAndInnerRelationsHistory(
                            $foreign_object->table_name,
                            $foreign_object->changes['id'],
                            $start_date, $end_date,
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
                    $res = array_merge($inner_data, $history_object->changes);
                    $resulting_data = $res;
                    self::setVal($nested, $current_scope, $res);
                    $already_added_history[$history_object->id] = true;
                }
            }
        }
        foreach($this_cfg['many_to_many_relations'] as $many_relation){
            self::get_many_to_many_realtions_history($many_relation, $original_id);
        }
        return $resulting_data;
    }

    public function test5($table, $original_id)
    {
        $this_cfg = self::$tableName_historyConfigs[$table]::get_cfg();
        $created = self::objectAndInnerRelationsFirstCreated($table, $original_id);
        $current = self::objectAndInnerRelationsCurrentCreated($table, $original_id);
        $main_object = self::getCurrentObject($table, $original_id);
        $start_time = $main_object->getAttributes()['created_at'];
        $end_time = self::getEndInterval($start_time, 1920);
        $nested = ([]);
        $already_added_objs = ([]);
        self::objectAndInnerRelationsHistory($table,
            $original_id,
            '2020-02-01 16:43:25',
            '2030-02-01 16:43:25',
            $nested,
            [$this_cfg['front_one_name']],
            $already_added_objs,
        );
        ksort($nested);
        $total_count = count($nested);
        $nested = self::combine_arrays_by_time($nested);
        $nested[now()->format('Y-m-d H:i:s.u')][] = [$this_cfg['front_one_name'] => $current];
        $created_array = [
            self::addMiliseconds(date_create($created['created_at']))->format(self::$date_format) =>
                array([$this_cfg['front_one_name'] => $created])
        ];
        $result = $created_array + $nested;
        return ([
            'all_history'=>$result,
            'merged'=>count($result),
            'total'=>$total_count + 1
        ]);
    }

    private static function getEndInterval($start_date, $hours){
        $end_date = new \DateTime($start_date);
        $end_date->add(new \DateInterval('PT'.$hours.'H'));
        return $end_date->format(self::$date_format);
    }

    private static function combine_arrays_by_time($input){
        $result_array = array();
        $MERGE_THRESHOLD = 1.5; // seconds
        $temp = array();
        $last_time = null;
        foreach($input as $time => $data){
             if ($last_time === null){
                 $last_time = $time;
                 $temp[] = $data;
                 continue;
             }
             if (abs(strtotime($time) - strtotime($last_time)) > $MERGE_THRESHOLD) {
                 $result_array[$last_time] = $temp;
                 $temp = array();
                 $last_time = $time;
             }
            $temp[] = $data;
        }
        if (count($temp) > 0) {
            $result_array[$last_time] = $temp;
        }
        return $result_array;
    }

}