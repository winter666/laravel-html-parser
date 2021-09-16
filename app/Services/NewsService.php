<?php


namespace App\Services;


use App\Models\Parser\News;

class NewsService
{

    public function getAll()
    {
        return News::query()
            ->orderBy('id', 'desc')
            ->get();
    }

    public function getById($id) {
        return News::find($id);
    }

    public function getByService($service) {
        return News::where('load_service', $service)
            ->orderBy('id', 'desc')
            ->get();
    }



}
