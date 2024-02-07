<?php

namespace App\HistoryConfigs;

use App\Models\User;

class UserHistoryConfig extends HistoryBaseConfig{
    protected static $table_name = 'users';
    protected static $model = User::class;
    protected static $front_one_name = 'user';
    protected static $front_many_name = 'users';
    protected static $exclude_fields = ['email_verified_at', 'created_at', 'updated_at'];

    protected static $relations = ([
        'tickets' => 'tickets',
    ]);
}