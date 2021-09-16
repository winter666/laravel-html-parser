<?php

namespace App\Http\Controllers;

use App\Services\LoadService;

class HomeController extends Controller
{
    public function index(LoadService $service)
    {
        $serviceList = LoadService::ALLOW_SERVICES_NAMES;

        return view('index', compact('serviceList'));
    }
}
