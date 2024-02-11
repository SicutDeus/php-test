<?php

namespace App\HistoryConfigs;

use App\Models\Distributor;
use App\Models\DistributorTicket;
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

    public static $many_to_many_relations = ([
        'distributors' => ([
            'through_table' => 'distributor_tickets',
            'through_model' => DistributorTicket::class,
            'self_id' => 'tickets_id',
            'outer_id' => 'distributor_id',
            'outer_model' => Distributor::class,
            'outer_table' => 'distributors'
        ])
    ]);
}