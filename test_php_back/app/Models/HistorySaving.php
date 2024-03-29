<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorySaving extends Model
{
    use HasFactory;
    protected $casts = [
        'changes' => 'array',
    ];
    protected $guarded = false;
}
