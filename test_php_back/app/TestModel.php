<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TestModel extends Model{
//    public static function boot()
//    {
//        parent::boot();
//        self::updating(function($model){
//            dd($model);
//        });
//        dd('xui');
//    }
    public static function updating($callback){
        dd($callback);
    }
}