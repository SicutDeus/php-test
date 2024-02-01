<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\HistorySaving;
use App\Models\Image;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Client\HttpClientException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class TestController extends Controller
{
    public function testFunc()
    {
//        $user = User::inRandomOrder()->first();
//        $user->name = fake()->name;
//        $user->email = fake()->unique()->safeEmail;
//        $user->save();
//        return response()->json($user->toArray());

//        $ticket = Ticket::inRandomOrder()->first();
//        $ticket->name = fake()->name;
//        $ticket->save();
//        return response()->json($ticket->toArray());


        $event = Event::inRandomOrder()->first();
        $event->name = fake()->name;
        $event->save();
        return response()->json($event->toArray());
    }
    public function testFunc2($table, $original_id)
    {
        $hist = DB::table('history_savings')->where('table_name', $table)->where('original_id', $original_id)->get();
        return response()->json($hist->toArray());
    }
}
