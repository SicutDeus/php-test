<?php

namespace App\HistoryConfigs;

use App\Models\Theater;
use App\Models\Ticket;
use App\Models\User;

class TheaterHistoryConfig extends HistoryBaseConfig{
    protected static $table_name = 'theaters';
    protected static $front_one_name = 'theater';
    protected static $front_many_name = 'theaters';
    protected static $model = Theater::class;

    public static  $foreign_tables = ([
        'district_id' => ([
            'table' => 'districts',
            'name' => 'district',
        ]),

    ]);
}