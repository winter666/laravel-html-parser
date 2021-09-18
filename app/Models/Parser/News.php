<?php

namespace App\Models\Parser;

use Illuminate\Database\Eloquent\Model;

/**
 * @property $id
 * @property $source_link
 * @property $content
 * @property $topic
 * @property $external_datetime
 * @property $load_service
 * @property $news_detail_id
 **/
class News extends Model
{
    protected $table = 'news';
    protected $fillable = [
        'source_link',
        'content',
        'topic',
        'external_datetime',
        'load_service',
        'news_detail_id'
    ];

    public function detail() {
        return $this->hasOne(NewsDetail::class, 'news_id', 'id');
    }
}
