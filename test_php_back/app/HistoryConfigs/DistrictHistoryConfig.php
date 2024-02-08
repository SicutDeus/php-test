<?php

namespace App\HistoryConfigs;

use App\Models\District;
use App\Models\Theater;
use App\Models\Ticket;
use App\Models\User;

class DistrictHistoryConfig extends HistoryBaseConfig{
    protected static $table_name = 'districts';
    protected static $front_one_name = 'district';
    protected static $front_many_name = 'districts';
    protected static $model = District::class;
}