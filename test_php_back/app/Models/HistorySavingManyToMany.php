<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorySavingManyToMany extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $guarded = false;
    protected $casts = [
        'first_data' => 'array',
        'second_data' => 'array',
    ];
}
