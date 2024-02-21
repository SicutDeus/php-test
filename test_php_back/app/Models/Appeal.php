<?php

namespace App\Models;

use App\Events\CreateObjectEvent;
use App\Events\SaveObjectEvent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appeal extends Model
{
    use HasFactory;
    protected $guarded = false;
    protected $dispatchesEvents = [
        'updated' => SaveObjectEvent::class,
        'created' => CreateObjectEvent::class,
    ];

    public function sellers(){
        return $this->hasManyThrough(
            Seller::class,
            SellerAppeal::class,
            'appeal_id',
          'id',
            'id',
            'seller_id',
        );
    }
}
