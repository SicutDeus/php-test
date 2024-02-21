<?php

namespace App\HistoryConfigs;

use App\Models\Distributor;
use App\Models\District;
use App\Models\Product;
use App\Models\Seller;
use App\Models\Theater;
use App\Models\Ticket;
use App\Models\User;

class ProductHistoryConfig extends HistoryBaseConfig{
    protected static $table_name = 'products';
    protected static $front_one_name = 'product';
    protected static $front_many_name = 'products';
    protected static $model = Product::class;

    protected static $oneToMany = ['appeal_id' => 'appeals'];//table_name


}