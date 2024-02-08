<?php

namespace App\Http\Controllers;

use App\HistoryConfigs\EventHistoryConfig;
use App\HistoryConfigs\TicketHistoryConfig;
use App\HistoryConfigs\UserHistoryConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\HistorySaving;

class FifthVersion extends Controller
{
    private static $tableName_historyConfigs = ([
        'users' => UserHistoryConfig::class,
        'tickets' => TicketHistoryConfig::class,
        'events' => EventHistoryConfig::class,
    ]);
    public function test5($table, $original_id)
    {
        $all_history = ([]);

        $main_cfg = self::$tableName_historyConfigs[$table]::get_cfg();
        $first_created_object = HistorySaving
            ::where('original_id', $original_id)
            ->where('table_name', $main_cfg['table_name'])
            ->first();
        $all_history[] = $first_created_object->changes;
        return (['all_history'=>$all_history]);
    }
}
