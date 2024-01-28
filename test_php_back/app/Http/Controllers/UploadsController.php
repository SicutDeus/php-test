<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UploadsController extends Controller
{
    public function get_upload($slug)
    {
        return "<img src=".asset('uploads/'.$slug).'>';
    }
}
