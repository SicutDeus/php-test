<?php

namespace App\HistoryConfigs;

use App\Models\Ticket;
use App\Models\User;

class TicketHistoryConfig extends HistoryBaseConfig{
    protected static $table_name = 'tickets';
    protected static $front_one_name = 'ticket';
    protected static $front_many_name = 'tickets';
    protected static $model = Ticket::class;


    public static  $foreign_tables = ([
        'event_id' => ([
            'table' => 'events',
            'name' => 'event',
        ]),
        'user_id' =>([
            'table' => 'users',
            'name' => 'user',
        ]),
    ]);
}