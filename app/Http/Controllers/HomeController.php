<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Client;
use App\Models\Project;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        return view('home.index');
    }

}
