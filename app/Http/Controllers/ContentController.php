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

    public function showContentDetail(NewsService $newsService, $serviceKey, $newsId) {
        $news = $newsService->getById($newsId);
        if ($news && $news->detail) {
            return view('contents.detail', compact('news'));
        }
        return redirect(404);
    }
}
