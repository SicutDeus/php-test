<?php

namespace App\HistoryConfigs;

use App\Models\Event;
use App\Models\Ticket;
use App\Models\User;

class EventHistoryConfig extends HistoryBaseConfig{
    protected static $table_name = 'events';
    protected static $front_one_name = 'event';
    protected static $front_many_name = 'events';
    protected static $model = Event::class;

}