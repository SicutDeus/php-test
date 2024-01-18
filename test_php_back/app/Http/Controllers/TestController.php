<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function testFunc()
    {
        return Ticket::all();
    }
}
