<?php

namespace App\HistoryConfigs;

use App\Models\Appeal;
use App\Models\Distributor;
use App\Models\District;
use App\Models\Theater;
use App\Models\Ticket;
use App\Models\User;

class AppealsHistoryConfig extends HistoryBaseConfig{
    protected static $table_name = 'appeals';
    protected static $front_one_name = 'appeal';
    protected static $front_many_name = 'appeals';
    protected static $model = Appeal::class;

    protected static $manyToMany = ['sellers' => 'sellers'];//table_name

    protected static  $fromOtherTables = ['products'];
    public static  $foreign_tables = ([
        'district_id' => ([
            'table' => 'districts',
            'name' => 'districts',
        ]),
    ]);

}