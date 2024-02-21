<?php

namespace App\Http\Controllers\History;

use App\HistoryConfigs\AppealsHistoryConfig;
use App\HistoryConfigs\DistributorHistoryConfig;
use App\HistoryConfigs\DistrictHistoryConfig;
use App\HistoryConfigs\EventHistoryConfig;
use App\HistoryConfigs\ProductHistoryConfig;
use App\HistoryConfigs\SellersHistoryConfig;
use App\HistoryConfigs\TheaterHistoryConfig;
use App\HistoryConfigs\TicketHistoryConfig;
use App\HistoryConfigs\UserHistoryConfig;
use App\Models\HistorySaving;
use Carbon\Carbon;

class HistoryBase
{
    public static $date_format = 'Y-m-d H:i:s.u';
    public static $tableNamesHistoryConfigs = ([
        'users' => UserHistoryConfig::class,
        'tickets' => TicketHistoryConfig::class,
        'events' => EventHistoryConfig::class,
        'theaters' => TheaterHistoryConfig::class,
        'districts' => DistrictHistoryConfig::class,
        'distributors' => DistributorHistoryConfig::class,
        'sellers' => SellersHistoryConfig::class,
        'appeals' => AppealsHistoryConfig::class,
        'products' => ProductHistoryConfig::class,
    ]);

    protected static function getHistoryOrFirstCreatedObject(
        $tableName,
        $original_id,
        $is_first_created = 1,
        $start_date = null,
        $end_date = null
    ) {
        $history = HistorySaving::
        where('table_name', $tableName)
            ->where('original_id', $original_id)
            ->orderBy('change_made_at', 'ASC');
        if ($start_date && $end_date) {
            $history = $history->whereBetween('change_made_at', [$start_date, $end_date]);
        }
        else if ($start_date){
            $history = $history->where('change_made_at', '>', $start_date);
        }
        return $is_first_created ? $history->where('first_created', $is_first_created)->first() : $history->get();
    }

    protected static function setVal(&$data, $scope, $value)
    {
        $level = &$data[$value['change_made_at']];
        $len = count($scope);
        for ($i=0; $i<$len; $i++) {
            $level = &$level[$scope[$i]];
        }
        $level = array_diff_key($value, array_flip(['change_made_at']));
    }
    public static function combineArrayByTime($input)
    {
        $result_array = array();
        $MERGE_THRESHOLD = 1; // seconds
        $temp = array();
        $last_time = null;
        foreach ($input as $time => $data) {
            if ($last_time === null) {
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

    public static function getEndTime($date){
        if (is_string($date)){
            $date = Carbon::createFromFormat(self::$date_format, $date);
        }
        $daysToAdd = 360;
        $date = $date->addDays($daysToAdd);
        return $date;
    }
}