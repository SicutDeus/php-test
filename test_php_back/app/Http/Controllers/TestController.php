<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Image;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Client\HttpClientException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class TestController extends Controller
{
    public function testFunc()
    {
        return \Storage::get();
        $my_image = Image::find(1);
        $src = asset($my_image->url);
        return $src;
    }
}
