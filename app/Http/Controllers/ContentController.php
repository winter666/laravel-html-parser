<?php

namespace App\Http\Controllers;

use App\Services\LoadService;
use App\Services\NewsService;
use Illuminate\Http\Request;

class ContentController extends Controller
{
    public function showByService(NewsService $service, $serviceKey) {
        $newsList = $service->getByService($serviceKey);
        $serviceName = LoadService::getName($serviceKey);

        return view('contents.index', compact('newsList', 'serviceName'));
    }
}
