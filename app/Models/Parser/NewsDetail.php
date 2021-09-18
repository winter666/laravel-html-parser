<?php

namespace App\Models\Parser;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * @property $id
 * @property $content
 * @property $attachments
 * @property $load_service
 * @property $source_link
 * @property $news_id
 **/
class NewsDetail extends Model
{
    protected $table = "news_details";
    protected  $fillable = ['content', 'attachments', 'load_service', 'source_link', 'news_id'];

    public function news() {
        return $this->belongsTo(News::class);
    }

    public function createData(News $news, string $content, array $attachments = []) {
        if (!trim($content)) {
            throw new \Exception('Field content cannot be empty');
        }
        $newsDetail = self::updateOrCreate([
            'source_link' => $news->source_link,
            'news_id' => $news->id
        ], [
            'content' => $content,
            'attachments' => json_encode([]),
            'load_service' => $news->load_service,
            'source_link' => $news->source_link,
            'news_id' => $news->id,
        ]);
        $news->news_detail_id = $newsDetail->id;
        $news->save();
        if (!empty($attachments)) {
            self::appendAttachment($newsDetail, $attachments);
        }

        return $newsDetail;
    }

    public static function appendAttachment(NewsDetail $newsDetail, array $data) {
        $attachments = [];
        foreach ($data as $attachment) {
            $attachmentName = $attachment['name'];
            $attachmentSrc = $attachment['src'];
            $fullPath = 'public/' . $newsDetail->load_service . "/" . $newsDetail->id . "/$attachmentName.jpg";
            Storage::put($fullPath, $attachmentSrc);
            $attachments[] = Storage::url($fullPath);
        }
        $newsDetail->attachments = json_encode($attachments);
        $newsDetail->save();
    }
}
