<?php

namespace App\HistoryConfigs;

use App\Models\Ticket;
use App\Models\User;

class TicketHistoryConfig extends HistoryBaseConfig{
    protected static $table_name = 'tickets';
    protected static $front_one_name = 'ticket';
    protected static $front_many_name = 'tickets';
    protected static $model = Ticket::class;

    protected static  $relations = ([
        'events' => 'event',
    ]);

    public static  $foreign_tables = ([
        'event_id' => 'events',
        'user_id' => 'users',
    ]);
}