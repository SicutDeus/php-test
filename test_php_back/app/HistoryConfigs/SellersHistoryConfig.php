<?php

namespace App\HistoryConfigs;

use App\Models\Distributor;
use App\Models\District;
use App\Models\Seller;
use App\Models\Theater;
use App\Models\Ticket;
use App\Models\User;

class SellersHistoryConfig extends HistoryBaseConfig{
    protected static $table_name = 'sellers';
    protected static $front_one_name = 'seller';
    protected static $front_many_name = 'seller';
    protected static $model = Seller::class;

}