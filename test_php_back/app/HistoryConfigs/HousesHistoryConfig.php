<?php

namespace App\HistoryConfigs;

use App\Models\Distributor;
use App\Models\District;
use App\Models\House;
use App\Models\Product;
use App\Models\Seller;
use App\Models\Theater;
use App\Models\Ticket;
use App\Models\User;

class HousesHistoryConfig extends HistoryBaseConfig{
    protected static $table_name = 'houses';
    protected static $front_one_name = 'house';
    protected static $front_many_name = 'houses';
    protected static $model = House::class;


}