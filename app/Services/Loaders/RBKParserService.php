<?php


namespace App\Services\Loaders;


use App\Interfaces\PageParser;
use App\Models\Parser\News;
use App\Services\Parser\ParserItemService;
use App\Services\Parser\ParserService;
use App\Services\Request\RequestService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class RBKParserService implements PageParser
{

    private $request;
    private $news;

    const SERVICE_KEY = 'rbk';
    const SERVICE_NAME = 'РБК';

    public function __construct()
    {
        $this->request = new RequestService('https://rt.rbc.ru/');
    }


    public function run () {
        $arrayNews = [];

        $response = $this->request->getPage();
        $parser = new ParserService($response);

        $newsBlockId = 'js_news_feed_banner';
        $newsListBlockClass = 'js-news-feed-list';
        $newsItemClass = 'news-feed__item';
        $newsItemTitleClass = $newsItemClass . '__title';
        $newsItemTimeClass = $newsItemClass . '__date';

        $newsBlock = $parser->elementById($newsBlockId)
            ->elementsByTagName('div')
            ->filterByClass($newsListBlockClass);

        $newsLinkList = $newsBlock->first()->getChildren($newsItemClass);
        if ($newsLinkList->count()) {
            $arrayNews = [];
            foreach ($newsLinkList as $newsLink) {
                $childrenCollection = $newsLink->getChildren();
                $spanTime = $childrenCollection->getItemByClass($newsItemTimeClass);
                $spanTimeTextContent = $this->getTextContentWrap($spanTime);
                $parsedSpanTimeBlock = $this->parseTimeBlock($spanTimeTextContent);

                $arrayNews[] = [
                    'source_link' => $newsLink->getAttrByName('href'),
                    'content' => $this->getTextContentWrap($childrenCollection->getItemByClass($newsItemTitleClass)),
                    'topic' => (isset($parsedSpanTimeBlock['content'])) ? $parsedSpanTimeBlock['content'] : null,
                    'external_date' => (isset($parsedSpanTimeBlock['date'])) ? $parsedSpanTimeBlock['date'] : null,
                    'external_time' => (isset($parsedSpanTimeBlock['time'])) ? $parsedSpanTimeBlock['time'] : null,
                    'load_service' => self::SERVICE_KEY
                ];
            }
        }
        $this->news = $arrayNews;

        return $arrayNews;
    }


    private function parseTimeBlock(string $text): array {
        $arrValues = explode(',', $text);
        $arrRes = [];
        foreach ($arrValues as $explodedStr) {
            $content = trim($explodedStr);
            if (strlen($content)) {
                if (preg_match('/\d{2}:\d{2}/', $content)) {
                    $arrRes['time'] = $content;
                } elseif (preg_match('/\d{1,2}\s\W{1,5}/', $content)) {
                    $arrRes['date'] = $content;
                } else {
                    $arrRes['content'] = $content;
                }
            }
        }

        return $arrRes;
    }


    private function getTextContentWrap(?ParserItemService $parserItemService): ?string {
        if (!empty($parserItemService)) {
            return $parserItemService->getTextContent();
        }
        return null;
    }


    public function saveData(int $limit = 15) {
        $count = 0;
        foreach ($this->news as $key => $item) {
            if ($count < $limit) {
                $count++;
            } else {
                break;
            }

            $validator = Validator::make($item, [
                'source_link' => 'required',
                'content' => 'required|string|max:255',
                'topic' => 'nullable|string',
                'external_date' => 'nullable|string',
                'external_time' => 'string',
                'load_service' => 'string'
            ]);

            if ($validator->fails()) {
                Log::error('Error on Validation content from ' . self::SERVICE_KEY . ': ' . print_r($item, true));
                unset($this->news[$key]);
                continue;
            }

            $this->serializeBeforeSave($item);
            News::updateOrCreate([
                'source_link' => $item['source_link'],
                'content' => $item['content']
            ], $item);
        }
    }

    private function serializeBeforeSave(&$item) {
        $dateTimeStr = '';
        if (!empty($item['external_date'])) {
            $dateTimeStr .= $item['external_date'];
        }
        if (!empty($item['external_time'])) {
            $dateTimeStr .= (strlen($dateTimeStr) > 0) ?  ', ' . $item['external_time'] : $item['external_time'];
        }
        unset($item['external_date']);
        unset($item['external_time']);
        $item['external_datetime'] = $dateTimeStr;
    }

}
