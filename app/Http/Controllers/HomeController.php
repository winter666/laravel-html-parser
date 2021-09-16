<?php

namespace App\Http\Controllers;

use App\Services\NewsService;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(NewsService $service)
    {
        $newsList = $service->getAll();

        return view('index', compact('newsList'));
    }
}
