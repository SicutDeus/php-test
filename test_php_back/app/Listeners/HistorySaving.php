<?php

namespace App\Listeners;

use App\Events\SaveObjectEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class HistorySaving
{

    public function handle(SaveObjectEvent $event): void
    {
        //
    }
}
