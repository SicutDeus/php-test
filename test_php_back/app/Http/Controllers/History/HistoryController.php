<?php

namespace App\Http\Controllers\History;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function getObjectHistory($table, $original_id)
    {
        $this_cfg = HistoryBase::$tableNamesHistoryConfigs[$table]::get_cfg();
        $created = HistoryFirstCreated::objectAndInnerRelationsFirstCreated($table, $original_id);
        $current = HistoryCurrent::objectAndInnerRelationsCurrentObject($table, $original_id);
        $nested = ([]);
        $already_added_objs = ([]);
        HistoryAll::objectAndInnerRelationsHistory(
            $table,
            $original_id,
            $created['created_at'],
            HistoryBase::getEndTime($created['created_at'])->format(HistoryBase::$date_format),
            $nested,
            [$this_cfg['front_one_name']],
            $already_added_objs,
        );
        ksort($nested);
        $total_count = count($nested);
        $nested = HistoryBase::combineArrayByTime($nested);
        $nested[now()->format('Y-m-d H:i:s.u')][] = [$this_cfg['front_one_name'] => $current];
        $created_array = [
            date_create($created['created_at'])->format(HistoryBase::$date_format) =>
                array([$this_cfg['front_one_name'] => $created])
        ];
        $result = $created_array + $nested;
        return ([
            'all_history'=>$result,
            'merged'=>count($result) - 2,
            'total'=>$total_count
        ]);
    }
}
