<?php

namespace App\HistoryConfigs;

use App\Models\Distributor;
use App\Models\District;
use App\Models\Theater;
use App\Models\Ticket;
use App\Models\User;

class DistributorHistoryConfig extends HistoryBaseConfig{
    protected static $table_name = 'distributors';
    protected static $front_one_name = 'distributor';
    protected static $front_many_name = 'distributors';
    protected static $model = Distributor::class;
}