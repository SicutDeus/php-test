<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorySavingAllObject extends Model
{
    use HasFactory;
    protected $casts = [
        'old_object_data' => 'array',
        'new_object_data' => 'array',
    ];
    protected $guarded = false;
}
