<?php

namespace App\Http\Controllers;

use App\Models\District;
use App\Models\Event;
use App\Models\HistorySaving;
use App\Models\Image;
use App\Models\Theater;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Client\HttpClientException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\Types\Null_;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use \Illuminate\Database\Eloquent\Collection;

class FirstWorkingVersion extends Controller
{
    public function testFunc()
    {
        for ($i = 0; $i < 300; $i++){
            sleep(2);
            $ticket = Ticket::find(1);
            $rand = rand(0,5);
            if ($rand == 1){
                $ticket->name = fake()->name;
                $ticket->save();
            }
            $user = User::find($ticket->user_id);
            $rand = rand(0,20);
            if ($rand==1){
                $ticket->user_id = User::inRandomOrder()->first()->id;
                $ticket->save();
            }

            $user = User::find($ticket->user_id);
            $rand = rand(0,5);
            if ($rand == 1){
                $user->name = fake()->name;
                $user->save();
            }
            $event = Event::find($ticket->event_id);
            $rand = rand(0,20);
            if ($rand == 1){
                $ticket->event_id = Event::inRandomOrder()->first()->id;
                $ticket->save();
            }

            $event = Event::find($ticket->event_id);
            $theater = Theater::find($event->theater_id);
            $rand = rand(0,5);
            if ($rand == 1){
                $theater->name = fake()->name;
                $theater->save();
            }

            $event = Event::find($ticket->event_id);
            $theater = Theater::find($event->theater_id);
            $rand = rand(0,20);
            if ($rand == 1){
                $event->theater_id = Theater::inRandomOrder()->first()->id;
                $event->save();
            }

            $event = Event::find($ticket->event_id);
            $theater = Theater::find($event->theater_id);
            $rand = rand(0,5);
            if ($rand == 1){
                $theater->name = fake()->name;
                $theater->save();
            }

            $event = Event::find($ticket->event_id);
            $theater = Theater::find($event->theater_id);
            $rand = rand(0,20);
            if ($rand == 1){
                $theater->district_id = District::inRandomOrder()->first()->id;
                $theater->save();
            }

            $event = Event::find($ticket->event_id);
            $theater = Theater::find($event->theater_id);
            $district = District::find($theater->district_id);
            $rand = rand(0,5);
            if ($rand == 1){
                $district->name = fake()->name;
                $district->save();
            }

            $event = Event::find($ticket->event_id);
            $rand = rand(0,5);
            if ($rand == 1){
                $event->name = fake()->name;
                $event->save();
            }
        }

        return response()->json('success');
    }

    public function seedChangeFunc()
    {
        for($i = 0; $i < 25; $i++){
            $ticket = Ticket::find(1);
            $ticket->name = fake()->name;
            if (rand(0, 7) == 6){
                $ticket->event_id = Event::inRandomOrder()->first()->id;

            };
            if (rand(0, 7) == 6){
                $ticket->user_id = User::inRandomOrder()->first()->id;
            };
            $ticket->save();
        }

        for($i = 0; $i < 25; $i++){
            $event = Event::inRandomOrder()->first();
            $event->name = fake()->unique()->name;
            $event->date = fake()->date;
            $event->save();
        }
        for($i = 0; $i < 25; $i++){
            $user = User::inRandomOrder()->first();
            $user->name = fake()->name;
            $user->save();
        }

        return response()->json('success');
    }
    public function testFunc2($table, $original_id)
    {
        $hist = DB::table('history_savings')->where('table_name', $table)->where('original_id', $original_id)->get();
        return response()->json($hist->toArray());
    }

    private function get_related_with_history($obj, $related_table_name, $inner_related = ([]), $method = Null)
    {
        if ($method === Null) {
            $method = $related_table_name;
        }
        $related_objs = $obj->$method;
        if (get_class($related_objs) != Collection::class) {
            $tmp = $related_objs;
            $related_objs = new Collection();
            $related_objs->add($tmp);
        }
        foreach ($related_objs as $related_obj) {
            $related_obj_history = DB::table('history_savings')->where('table_name', $related_table_name)->where('original_id', $related_obj->id)->get();
            $result_data = ([]);
            foreach ($inner_related as $key => $value) {
                $tmp = self::get_related_with_history($related_obj->replicate(), $key, method: $value);
                if (!empty($tmp) and count($tmp) > 0) {
                    $related_obj[$key] = $tmp;
                }
            }
            $result_data[] = ([
                substr($related_table_name, 0, -1) => $related_obj,
                'history' => $related_obj_history,
            ]);
        }
        return $result_data;
    }

    private function get_related_with_history2($obj, $related_table_name, $inner_related = ([]), $method = Null)
    {
        if ($method === Null) {
            $method = $related_table_name;
        }
        $related_objs = $obj->$method;
        if (get_class($related_objs) != Collection::class) {
            $tmp = $related_objs;
            $related_objs = new Collection();
            $related_objs->add($tmp);
        }
        $result_data = ([]);
        foreach ($related_objs as $related_obj) {
            $related_data = $related_obj->toArray();
            $related_obj_history = DB::table('history_savings')->where('table_name', $related_table_name)->where('original_id', $related_obj->id)->get();
            foreach ($inner_related as $key => $value) {
                $tmp = self::get_related_with_history2($related_obj, $value['table_name'], $value['inner_related'],  method: $value['method']);
                if (!empty($tmp) and count($tmp) > 0) {
                    $related_data[$key] = $tmp;
                }
            }
            $result_data[] = ([
                substr($related_table_name, 0, -1) => $related_data,
                'history' => $related_obj_history,
            ]);
        }
        return $result_data;
    }

    public function testFunc3($table, $original_id)
    {
        $mapper = ([
            'users' => ([
                'model' => User::class,
                'relations' => ([
                    'tickets' => function ($obj){
                       return $this->get_related_with_history2($obj, 'tickets', inner_related: ([
                           'events' => ([
                              'table_name' => 'events',
                               'method' => 'event',
                               'inner_related' => ([]),
                           ]),
                       ]));
                    }
                ]),
            ]),
            'tickets' => ([
                'model' => Ticket::class,
                'relations' => ([
                    'event' => function ($obj){
                        return $this->get_related_with_history2($obj, 'events', method: 'event');
                    },
                    'user' => function ($obj){
                        return $this->get_related_with_history2(
                            $obj,
                            related_table_name: 'users',
                            inner_related: ([
                                'tickets' => ([
                                    'table_name' => 'tickets',
                                    'method' => 'tickets',
                                    'inner_related' => ([
                                        'events' => ([
                                            'table_name' => 'events',
                                            'method' => 'event',
                                            'inner_related' => ([]),
                                        ]),
                                    ])
                                ]),
                            ]),
                            method: 'user'
                        );
                    }
                ])
            ])
        ]);

        $main_object = $mapper[$table]['model']::find($original_id);
        $result = (['data' => ([])]);
        $result['data'][substr($table, 0, -1)] = $main_object->toArray();
        foreach ($mapper[$table]['relations'] as $key => $value) {
            $result['data'][substr($table, 0, -1)][$key] = $value($main_object);
        }
        $result['data']['history'] = DB::table('history_savings')->where('table_name', $table)->where('original_id', $original_id)->get();
        return $result;
    }
}
